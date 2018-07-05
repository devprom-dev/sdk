<?php

namespace Devprom\ApplicationBundle\Service;

use Devprom\ProjectBundle\Service\Project\ApplyTemplateService;
use Devprom\Component\HttpKernel\ServiceDeskAppKernel;

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class CreateProjectService
{
	private $skip_demo_data = false;
	private $portfolioId = '';
	private $programId = '';
	
 	function execute( $parms )
 	{
 		$this->user_id = getSession()->getUserIt()->getId();
 		$this->code_name = $parms['CodeName'];
 		$this->caption = $parms['Caption'];
		$this->portfolioId = $parms['portfolio'];
		$this->programId = $parms['program'];
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
 		
 		$project_it = $this->createProject();
 		if ( ! $project_it instanceof \OrderedIterator ) return $project_it;

		if ( $this->portfolioId > 0 && $project_it->getId() > 0 ) {
			// join the project to the portfolio given
			if ( class_exists('ProjectGroupLink') ) {
				getFactory()->getObject('ProjectGroupLink')->add_parms(
					array (
						'ProjectGroup' => $this->portfolioId,
						'Project' => $project_it->getId()
					)
				);
			}
		}
        if ( $this->programId > 0 && $project_it->getId() > 0 ) {
            // make the project to be subproject of the program given
            $className = getFactory()->getClass('ProjectLink');
            if ( class_exists('ProjectLink') ) {
                $object = getFactory()->getObject($className);

                $parms = array();
                parse_str(\ProjectLinkTypeSet::SUBPROJECT_QUERY_STRING, $parms);

                $object->add_parms(
                    array_merge(
                        array (
                            'Source' => $this->programId,
                            'Target' => $project_it->getId()
                        ),
                        $parms
                    )
                );
            }
        }

		return $project_it;
 	}
 	
 	function createProject()
 	{
		global $model_factory, $session;
		
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );

		// check the use who creates a project is defined
		$user_it = getFactory()->getObject('User')->getExact($this->user_id);
		if ( $user_it->count() < 1 ) return -1;

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
		$parms['StartDate'] = "NOW()";
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
        $parms['Tools'] = $this->methodology;

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
        getFactory()->getEventsManager()->removeNotificator( new \PMChangeLogNotificator() );

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
            'Blog' => $blog_id
		);
		
		$prj_cls->modify_parms($project_it->getId(),$parms);
		$project_it = $prj_cls->getExact($project_it->getId());  

		$project_roles = $model_factory->getObject('ProjectRole');

		$lead_it = $project_roles->getByRef( 'ReferenceName', 'lead' );
		// check the template has been imported
		if ( $project_roles->getRecordCount() < 1 ) return -11;

		$rolesRegistry = $project_roles->getRegistry();
		// append additional (system) project roles
		$rolesRegistry->Merge(
			array (
				'Caption' => translate('Все пользователи'),
				'ReferenceName' => 'guest',
				'ProjectRoleBase' => '0'
			),
            array(
                'ReferenceName'
            )
		);

        $rolesRegistry->Merge(
			array (
				'Caption' => translate('Участники связанных проектов'),
				'ReferenceName' => 'linkedguest',
				'ProjectRoleBase' => '0'
			),
            array(
                'ReferenceName'
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

		if ( class_exists('TestingDocType') ) {
			$testType_it = getFactory()->getObject('TestingDocType')->getAll();
			if ( $testType_it->count() < 1 ) {
				$scenarioTypeId = $testType_it->object->add_parms(
					array(
						'Caption' => text('testing90'),
						'ReferenceName' => 'scenario',
						'PageReferenceName' => 3,
						'WikiEditor' => $project_it->get('WikiEditorClass')
					)
				);
				$testPlanTypeId = $testType_it->object->add_parms(
					array(
						'Caption' => text('testing89'),
						'ReferenceName' => 'section',
						'PageReferenceName' => 3,
						'WikiEditor' => $project_it->get('WikiEditorClass')
					)
				);
				$plan_it = $model_factory->getObject('TestScenario')->getRegistry()->Query(
					array(
						new \FilterVpdPredicate(),
                        new \FilterAttributeNullPredicate('PageType'),
						new \WikiRootFilter()
					)
				);
				while( !$plan_it->end() ) {
					$plan_it->object->modify_parms(
						$plan_it->getId(),
						array (
							'PageType' => $testPlanTypeId
						)
					);
					$plan_it->moveNext();
				}
				$scenario_it = $plan_it->object->getRegistry()->Query(
					array(
						new \FilterVpdPredicate(),
                        new \FilterAttributeNullPredicate('PageType'),
						new \WikiNonRootFilter()
					)
				);
				while( !$scenario_it->end() ) {
					$scenario_it->object->modify_parms(
						$scenario_it->getId(),
						array (
							'PageType' => $scenarioTypeId
						)
					);
					$scenario_it->moveNext();
				}
			}
		}

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

		return $project_it;
 	}
 	
 	function createByTemplate( $template_it, $project_it )
 	{
		getFactory()->getEventsManager()->removeNotificator( new \ChangesWaitLockReleaseTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheSessionProjectTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \CacheResetTrigger() );
		getFactory()->getEventsManager()->removeNotificator( new \PMChangeLogNotificator() );
		getFactory()->getEventsManager()->removeNotificator( new \EmailNotificator() );

        getFactory()->getObject('pm_Methodology')->add_parms(
            array(
                'Project' => $project_it->getId(),
                'IsReleasesUsed' => 'Y',
                'IsPlanningUsed' => 'Y'
            )
        );

		$service = new ApplyTemplateService();
		$service->setResetState(false);
		
		// apply default template
		$service->apply(
				$template_it->getXml(),
				$project_it, 
				array(), // import all data available in the template
				$this->skip_demo_data ? array('ProjectArtefacts') : array()
		);

        $project_it->object->modify_parms( $project_it->getId(),
            array(
                'Tools' => $template_it->get('FileName')
            )
        );
 	}
 	
 	public function invalidateCache()
 	{
        foreach( array('sessions', 'projects') as $path ) {
            getFactory()->getCacheService()->invalidate($path);
        }
 	}

	public function invalidateServiceDeskCache()
	{
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
				
			case -7:
				return text(206);
				
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