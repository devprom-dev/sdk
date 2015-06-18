<?php

namespace Devprom\ApplicationBundle\Service;

use Devprom\ProjectBundle\Service\Project\ApplyTemplateService;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';
include "ActivateUserSettings.php";

class CreateProjectService
{
	private $skip_demo_data = false;
	
 	function execute( $parms )
 	{
 		$this->user_id = getSession()->getUserIt()->getId();
 		$this->code_name = $parms['CodeName'];
 		$this->caption = $parms['Caption'];
 		$this->skip_demo_data = !$parms['DemoData'];
 		
 		$template = getFactory()->getObject('pm_ProjectTemplate');
 		
 		$template->setRegistry( new \ObjectRegistrySQL() );
 		
 		$template_it = $template->getExact( $parms['Template'] );

 		if ( $template_it->count() > 0 )
 		{
	 		$this->language = $template_it->get('Language');
	 		$this->methodology = $template_it->get('FileName');
 		}
 		else
 		{
	 		$this->language = 'RU';
	 		$this->methodology = '1';
 		}
 		
 		$this->access = '';
 		
 		return $this->createProject();
 	}
 	
 	function createProject()
 	{
		global $model_factory, $session;
		
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );
		
		// check the use who creates a project is defined
		$user_cls = new \Metaobject('cms_User');
		$user_it = $user_cls->getExact($this->user_id);
		
		if($user_it->count() < 1) 
		{
			return -1;
		}
		
		// создаем проект
		$prj_cls = $model_factory->getObject('pm_Project');
		$prj_it = $prj_cls->getByRef('CodeName', $this->code_name);
		
		if( $prj_it->count() > 0 ) 
		{
			return -3;
		}

		$parms = array();

		$parms['CodeName'] = $this->code_name;
		$parms['Caption'] = $this->caption;
		$parms['StartDate'] = strftime('%d.%m.%Y');
		$parms['DaysInWeek'] = 5;
		
		if ( is_numeric($this->language) )
		{
			$parms['Language'] = $this->language;
		}
		else
		{
			switch($this->language) 
			{
				case 'RU':
					
					$parms['Language'] = 1;
					
					break;
					
				case 'EN':
					
					$parms['Language'] = 2;
					
					break;
			}
		}
		
		$project_id = $prj_cls->add_parms($parms);

		if( $project_id < 1 ) 
		{
			return -4;
		}

		$parms = array();
		$parms['VPD'] = \ModelProjectOriginationService::getOrigin($project_id);
		
		$prj_cls->modify_parms($project_id, $parms);
		
		$project_it = $prj_cls->getExact($project_id);
		
		// создаем участника
		$part_cls = $model_factory->getObject('pm_Participant');

		$parms = array();
		$parms['SystemUser'] = $user_it->getId();
		$parms['IsActive'] = 'Y';
		$parms['Project'] = $project_id;
		$parms['VPD'] = \ModelProjectOriginationService::getOrigin($project_id);
		
		$id = $part_cls->add_parms($parms);

		if( $id < 1 ) return -5; 

		$part_it = $part_cls->getExact($id);

		$model_factory->resetCachedIterator( $prj_cls );
		
		$auth_factory = new \AuthenticationFactory();
			
		$auth_factory->setUser( $user_it );
		
		$session = new \PMSession($project_it, $auth_factory);

		// включаем VPD
		getFactory()->enableVpd(true);
		
		$parms = array();
		
		// создаем блог проекта
		$blog = new \Metaobject('Blog');
		$parms['Caption'] = translate('Блог');
		$blog_id = $blog->add_parms($parms);
		
		// looking for template
		$template = $model_factory->getObject('pm_ProjectTemplate');
		
		$template->setRegistry( new \ObjectRegistrySQL() );
		
		$template_it = $template->getByRef( 'FileName', $this->methodology );

		// create the project from template
		$this->createByTemplate( $template_it, $project_it );

		$parms = array(
				'Blog' => $blog_id,
				'Tools' => $this->methodology
		);
		
		$prj_cls->modify_parms($project_it->getId(),$parms);
		$project_it = $prj_cls->getExact($project_it->getId());  

		$project_roles = $model_factory->getObject('ProjectRole');

		$lead_it = $project_roles->getByRef( 'ReferenceName', 'lead' );
		// check the template has been imported
		if ( $project_roles->getRecordCount() < 1 ) return -11;
		
		// append additional (system) project roles
		$role_id = $project_roles->add_parms(
			array (
				'Caption' => translate('Все пользователи'),
				'ReferenceName' => 'guest',
				'ProjectRoleBase' => '0'
			)
		);

		$role_id = $project_roles->add_parms(
			array (
				'Caption' => translate('Участники связанных проектов'),
				'ReferenceName' => 'linkedguest',
				'ProjectRoleBase' => '0'
			)
		);

		$role_cls = $model_factory->getObject('pm_ParticipantRole');

		$result_it = $role_cls->getRegistry()->Query(
				array (
						new \FilterAttributePredicate('Participant', $part_it->getId()),
						new \FilterAttributePredicate('ProjectRole', $lead_it->getId())
				)
		);
		
		if ( $result_it->getId() < 1 )
		{
			$parms['Participant'] = $part_it->getId();
			$parms['Capacity'] = 1;
			$parms['IsActive'] = 'Y';
			$parms['ProjectRole'] = $lead_it->getId();
			$role_cls->add_parms($parms);
		}

		$test_result = getFactory()->getObject('pm_TestExecutionResult');
		if ( $test_result->getRegistry()->Count(array(new \FilterAttributePredicate('ReferenceName', 'succeeded'))) < 1 )
		{
			$test_result->add_parms(
					array (
							'Caption' => translate('Пройден'),
							'ReferenceName' => 'succeeded'
					)
			);
		}
 		if ( $test_result->getRegistry()->Count(array(new \FilterAttributePredicate('ReferenceName', 'failed'))) < 1 )
		{
			$test_result->add_parms(
					array (
							'Caption' => translate('Провален'),
							'ReferenceName' => 'failed'
					)
			);
		}
		
		// turn on email notifications
		$notification = $model_factory->getObject('Notification');
		$notification->store( $project_it->getDefaultNotificationType(), $part_it );

		// add changed objects into the log
		$change_log = new \Metaobject('ObjectChangeLog');
		
		$parms['Caption'] = $part_it->getDisplayName();
		$parms['ObjectId'] = $part_it->getId();
		$parms['ClassName'] = strtolower(get_class($part_it->object));
		$parms['EntityName'] = $part_it->object->getDisplayName();
		$parms['ChangeKind'] = 'added';
		$parms['Author'] = $part_it->getId();
		$parms['Content'] = '';
		$parms['VisibilityLevel'] = 1;
		$parms['SystemUser'] = $this->user_id;
	
		$change_log->add_parms($parms);
		
		$parms['Caption'] = $project_it->getDisplayName();
		$parms['ObjectId'] = $project_it->getId();
		$parms['ClassName'] = strtolower(get_class($project_it->object));
		$parms['EntityName'] = $project_it->object->getDisplayName();
		$parms['ChangeKind'] = 'added';
		$parms['Author'] = $part_it->getId();
		$parms['Content'] = '';
		$parms['VisibilityLevel'] = 1;
		$parms['SystemUser'] = $this->user_id;
	
		$change_log->add_parms($parms);

		$this->invalidateCache();
		
		return $project_id;
 	}
 	
 	function createByTemplate( $template_it, $project_it )
 	{
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \PMChangeLogNotificator() );
		getFactory()->getEventsManager()->removeNotificator( new \EmailNotificator() );
		
		$meth_cls = getFactory()->getObject('pm_Methodology');
		
		$parms = array();
		$parms['Project'] = $project_it->getId();

		$methodology_it = $meth_cls->getExact( 
			$meth_cls->add_parms($parms) ); 

		$service = new ApplyTemplateService();
		$service->setResetState(false);
		
		// apply default template
		$service->apply(
				$template_it, 
				$project_it, 
				array(), // import all data available in the template
				$this->skip_demo_data ? array('ProjectArtefacts', 'Attributes') : array()
		);
 	}
 	
 	protected function invalidateCache()
 	{
		$lock = new \CacheLock();
		$lock->Locked(1) ? $lock->Wait(10) : $lock->Lock();
 		
 		getFactory()->getObject('ProjectCache')->resetCache();

	    $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
	    while( !$portfolio_it->end() )
	    {
	        getSession()->truncateForProject( $portfolio_it );
	        $portfolio_it->moveNext();
	    }
		
		$command = new \Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
    	$command->setContainer(ServiceDeskAppKernel::loadWithoutRequest()->getContainer()); 
    	
    	$output = new \Symfony\Component\Console\Output\NullOutput();
    	$command->run(new \Symfony\Component\Console\Input\ArgvInput(array('', '--no-warmup')), $output);
 	}
 	
	static function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return text(200);
				
			case -2:
				return text(201);
				
			case -3:
				return text(202);
				
			case -4:
				return text(203);

			case -5:
				return text(204);
				
			case -6:
				return text(205);
				
			case -7:
				return text(206);
				
			case -8:
				return text(207);
				
			case -9:
				return text(1870);
				
			case -10:
				return text(209);
				
			case -11:
				return text(1424);
				
			default:
				return text(229);
		}
	}     	
}