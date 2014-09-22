<?php

namespace Devprom\ApplicationBundle\Service;

use Devprom\ProjectBundle\Service\Project\ApplyTemplateService;

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include "ActivateUserSettings.php";

class CreateProjectService
{
 	function execute()
 	{
 		global $_REQUEST, $model_factory;
 		
 		$this->user_id = getSession()->getUserIt()->getId();
 		$this->code_name = $_REQUEST['Codename'];
 		$this->caption = $_REQUEST['Caption'];
 		
 		$template = $model_factory->getObject('pm_ProjectTemplate');
 		
 		$template->setRegistry( new \ObjectRegistrySQL() );
 		
 		$template_it = $template->getExact( $_REQUEST['Template'] );

 		if ( $template_it->count() > 0 )
 		{
	 		$this->language = $template_it->get('Language');
	 		$this->methodology = $template_it->get('FileName');
 		}
 		else
 		{
	 		$this->language = 'RU';
	 		$this->methodology = $_REQUEST['Methodology'] == '' ? '1' : $_REQUEST['Methodology'];
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
		
		$project_it->modify( $parms );

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

		// turn on email notifications
		$notification = $model_factory->getObject('Notification');
		$notification->store( 'every1hour', $part_it );

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
		
		getFactory()->getObject('ProjectCache')->resetCache();

	    $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
			    
	    while( !$portfolio_it->end() )
	    {
	        getSession()->truncateForProject( $portfolio_it );
			        
	        $portfolio_it->moveNext();
	    }
		
		return $project_id;
 	}
 	
 	function createByTemplate( $template_it, $project_it )
 	{
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \PMChangeLogNotificator() );
		getFactory()->getEventsManager()->removeNotificator( new \PMEmailNotificator() );
		
		$meth_cls = getFactory()->getObject('pm_Methodology');
		
		$parms = array();
		$parms['Project'] = $project_it->getId();

		$methodology_it = $meth_cls->getExact( 
			$meth_cls->add_parms($parms) ); 

		// create version numbering settings
		$settings_cls = getFactory()->getObject('pm_VersionSettings');

		$parms = array();
		$parms['Project'] = $project_it->getId();
		$settings_cls->add_parms($parms);
				
		$service = new ApplyTemplateService();
		
		$service->setResetState(false);
		
		// apply default template
		$service->apply($template_it, $project_it );
 	}
 	
 	function getSuccessMessage()
 	{
 		return text(229);
 	}
}