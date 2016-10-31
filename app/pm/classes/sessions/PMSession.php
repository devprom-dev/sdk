<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH.'core/c_session.php';
include_once SERVER_ROOT_PATH."co/classes/ResourceBuilderCoLanguageFile.php";

include SERVER_ROOT_PATH.'pm/classes/model/ModelFactoryProject.php';
include SERVER_ROOT_PATH.'pm/classes/model/permissions/AccessPolicyProject.php';

include SERVER_ROOT_PATH.'pm/classes/widgets/ModuleCategoryBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/widgets/ObjectsListWidgetBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/common/PMContextResourceBuilder.php';
include SERVER_ROOT_PATH.'pm/classes/common/PMContextResourceCustomReportsBuilder.php';

include SERVER_ROOT_PATH.'pm/classes/common/PMUserSettings.php'; 
include SERVER_ROOT_PATH."pm/classes/common/ModuleProjectsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsTasksBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsPlanBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ObjectMetadataCustomAttributesBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ObjectModelCustomAttributesBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ProjectArtifactMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/search/SearchableObjectsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ChangeLogEntitiesProjectBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/permissions/AccessRightEntitySetCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilderTask.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilderWikiPage.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilderRequest.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilderIssue.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilderTask.php";
include SERVER_ROOT_PATH."pm/classes/workflow/TransitionMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/workflow/events/ApplyBusinessActionsEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/workflow/events/ResetFieldsEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/resources/ResourceBuilderTerminology.php";
include SERVER_ROOT_PATH."pm/classes/widgets/BulkActionBuilderWorkflow.php";

include SERVER_ROOT_PATH."pm/classes/settings/DictionaryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/settings/WorkflowBuilderCommon.php";

include SERVER_ROOT_PATH."pm/views/common/PageSettingSet.php";
include SERVER_ROOT_PATH.'pm/views/common/PageSettingCommonBuilder.php';
include SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategyCommonBuilder.php";

include SERVER_ROOT_PATH."pm/classes/report/ReportsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/report/events/CustomReportModelEventsHandler.php";

include SERVER_ROOT_PATH."pm/classes/communications/QuestionMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataPermissionsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/RequestTriggersCommon.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/IssueOrderNumTrigger.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/IssueModifyProjectTrigger.php";
include SERVER_ROOT_PATH."pm/classes/issues/events/ResetTasksEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/issues/events/RequestFeatureUpdateMetricsEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/plan/events/MilestoneMetricsEventHandler.php";

include SERVER_ROOT_PATH."pm/classes/time/events/TimeSpentEvent.php";

include SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataPermissionsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/tasks/triggers/TaskOrderNumTrigger.php";
include SERVER_ROOT_PATH."pm/classes/tasks/TaskTypeMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/tasks/events/UpdateLeftWorkEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/tasks/events/TaskModifyProjectTrigger.php";

include SERVER_ROOT_PATH."pm/classes/plan/IterationMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/plan/ReleaseMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/plan/MilestoneMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/plan/events/ModifyIssuesVersionNumber.php";

include SERVER_ROOT_PATH."pm/classes/product/FeatureMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/product/ProjectTemplateArtefactsBuilderProduct.php";
include SERVER_ROOT_PATH."pm/classes/product/events/FeatureUpdateMetricsEventHandler.php";

include SERVER_ROOT_PATH."pm/classes/common/HistoricalObjectsRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilderLatest.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateArtefactsBuilderWorkItems.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectMetricsModelBuilderDates.php";
include SERVER_ROOT_PATH."pm/classes/project/predicates/ProjectLinkedSelfPredicate.php";

include SERVER_ROOT_PATH."pm/classes/common/triggers/CacheSessionProjectTrigger.php";
include SERVER_ROOT_PATH."pm/classes/communications/triggers/DeleteCommentsTrigger.php";

include_once SERVER_ROOT_PATH."pm/classes/notificators/EmailNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/notificators/PMChangeLogNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetWorkItemDatesTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetPlanItemDatesTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/ClearCommentsEvent.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/StoreTextChangesEvent.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/BusinessActionModifiedEvent.php";

include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiPageNewVersionTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiBreakTraceTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageMetadataBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/events/DocumentStateChangedEventHandler.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/events/RemoveObsoleteAttachmentsEventHandler.php";

///////////////////////////////////////////////////////////////////////
class PMSession extends SessionBase
{
 	protected $part_it;
    protected $project_roles;
    protected $project_it;
    protected $linked_it;
    protected $shareable;
    protected $module;
    protected $project_info = null;
 	
 	function __construct( $project_info, $factory = null, $builders = null, $cache_service = null )
 	{
 		$this->project_info = $project_info;
        parent::__construct( $factory, $builders, $cache_service );
 	}

 	function __sleep()
    {
        return array_merge( parent::__sleep(),
            array ('part_it', 'project_roles', 'project_it', 'linked_it', 'shareable', 'module', 'project_info')
        );
    }

    protected function buildFactories()
    {
        global $model_factory;

        // destroy services
        $model_factory->setAccessPolicy(null);
        $model_factory->setEntityOriginationService(null);

        $origination_service = $this->buildOriginationService(getCacheService());
        $origination_service->getCacheService()->setDefaultPath('pm-' . $this->getProjectIt()->get('VPD'));

        // reconfigure the cache
        $this->setCacheEngine(getCacheService());
        $this->getCacheEngine()->setDefaultPath($this->getCacheKey());

        // create the new model factory
        $model_factory = new ModelFactoryProject(
            $model_factory->getPluginsManager(),
            $this->getCacheEngine(),
            $this->buildAccessPolicy($this->getCacheEngine()),
            null,
            $origination_service
        );

        parent::buildFactories();

        WorkflowScheme::Reset();
    }

    public function configure()
	{
        $project_data = $this->buildProjectData(
            $this->findProject($this->project_info)
        );

        $this->project_it = $project_data['project'];
        $this->linked_it = $project_data['linked'];

        $this->resetCaches();

        $roles_data = $this->buildParticipantData();
        $this->part_it = $roles_data['participant'];
        $this->project_roles = $roles_data['roles'];

		parent::configure();

		$this->addBuilder(new ProjectTemplateSectionsRegistryBuilderLatest($this));
	}
 	
 	public function buildAccessPolicy( $cache_service )
 	{
 		return new AccessPolicyProject( $cache_service, $this );
 	}
 	
 	public function buildOriginationService( $cache_service )
 	{
 		return new ModelProjectOriginationService($this, $cache_service);
 	}
 	
 	function createBuilders()
 	{
 	    return array_merge( 
 	            array (
 	            		new ResourceBuilderCoLanguageFile(),
 	                    new CacheResetTrigger(),
 	            		new WikiPageMetadataBuilder(),
 	                    new SharedObjectsCommonBuilder(),
 	                    new SharedObjectsTasksBuilder(),
 	                    new SharedObjectsPlanBuilder(),
 	                    new PageSettingCommonBuilder(),
 	                    new ModuleProjectsBuilder(),
 	                    new ReportsCommonBuilder(),
 	                    new SearchableObjectsCommonBuilder(),
 	                    new AccessRightEntitySetCommonBuilder(),
 	                    new RequestMetadataBuilder(),
 	            		new TaskTypeMetadataBuilder(),
 	                    new TaskMetadataBuilder(),
 	                    new IterationMetadataBuilder(),
 	                    new ReleaseMetadataBuilder(),
 	                    new MilestoneMetadataBuilder(),
 	                    new RequestTriggersCommon(),
 	                    new IssueOrderNumTrigger(),
 	                    new TaskOrderNumTrigger(),
 	                    new SetWorkItemDatesTrigger(),
 	            		new SetPlanItemDatesTrigger(),
 	                    new DeleteCommentsTrigger(),
 	                    new IssueModifyProjectTrigger(),
 	            		new TaskModifyProjectTrigger(),
 	            		new FeatureMetadataBuilder(),
 	            		new StateBusinessActionBuilderTask(),
 	                    new StateBusinessActionBuilderRequest(),
						new StateBusinessActionBuilderWikiPage(),
 	                    new StateBusinessRuleBuilderIssue(),
 	                    new StateBusinessRuleBuilderTask(),
 	            		new DictionaryBuilderCommon($this),
 	            		new WorkflowBuilderCommon($this),
 	            		new HistoricalObjectsRegistryBuilderCommon(),
 	            		new ProjectTemplateSectionsRegistryBuilderCommon($this),
						new ProjectTemplateArtefactsBuilderProduct($this),
						new ProjectTemplateArtefactsBuilderWorkItems($this),
 	            		new TransitionMetadataBuilder(),
						new QuestionMetadataBuilder(),
						new ProjectArtifactMetadataBuilder(),
 	            		
 	            		// widgets
 	            		new ModuleCategoryBuilderCommon(),
 	            		new BulkActionBuilderWorkflow(),
 	            		new ObjectsListWidgetBuilderCommon(),
 	            		
 	            		// triggers
 	            		new WikiPageNewVersionTrigger(),
 	            		new DocumentStateChangedEventHandler(),
 	            		new WikiBreakTraceTrigger(),
 	            		new CustomReportModelEventsHandler(),
 	            		new FeatureUpdateMetricsEventHandler(),
 	            		new RequestFeatureUpdateMetricsEventHandler(),
						new ModifyIssuesVersionNumber(),
						new RemoveObsoleteAttachmentsEventHandler(),
                        new MilestoneMetricsEventHandler()
 	            ),
 	            parent::createBuilders(),
 	            array (
 	                    new ObjectMetadataCustomAttributesBuilder(),
 	            		new ObjectModelCustomAttributesBuilder(),
 	                    new ChangeLogEntitiesProjectBuilder(),
 	                    new EstimationStrategyCommonBuilder(),
 	                    new CacheSessionProjectTrigger(),
 	            		new CustomizableObjectBuilderCommon($this),
 	            		new PMContextResourceBuilder(),
 	            		new PMContextResourceCustomReportsBuilder(),
                        new ResourceBuilderTerminology(),

 	            		// model
 	            		new ResetFieldsEventHandler(),
 	            		new ApplyBusinessActionsEventHandler(),
 	            		new ResetTasksEventHandler(),
 	            		new UpdateLeftWorkEventHandler(),
 	            		new ClearCommentsEvent(),
                        new StoreTextChangesEvent($this),
						new TimeSpentEvent(),
                        new BusinessActionModifiedEvent(),
						new ProjectMetricsModelBuilderDates(),

                        new PMChangeLogNotificator(),
                        new EmailNotificator(),

						// latest to override custom builders
					    new RequestMetadataPermissionsBuilder(),
						new TaskMetadataPermissionsBuilder()
				)
 	    );
 	}
 	
 	private function resetCaches()
 	{
 		global $model_factory;
 		
 		$model_factory->resetCache();
 	}
 	
 	protected function findProject( $parms )
 	{
	 	if ($parms instanceof ProjectIterator ) return $parms->copy();

	 	if ( $parms instanceof OrderedIterator )
	 	{
			return getFactory()->getObject('Project')->getExact( $parms->getId() );
	 	}

		return getFactory()->getObject('Project')->getByRef('LCASE(CodeName)', strtolower(trim($parms,':')));
 	}
 	
 	protected function buildProjectData( $project_it )
 	{
 	    // cache methodology data
 		$result = array (
 		    'project' => $project_it
 		);
 		
	    $result['linked'] = $project_it->getId() > 0 
	    		? $project_it->getRef('LinkedProject')
	    		: $project_it->object->createCachedIterator( array() );
 		
 		return $result;
 	}
 	
 	protected function buildParticipantData()
 	{
 		$result = array ();
 		 
 		$part = new Participant();
 		
 		$user_it = $this->getUserIt();
 		
 		if ( !is_object($user_it) ) {
 			$result['participant'] = $part->createCachedIterator( array() );
 			return $result;
 		}
 		if ( $user_it->getId() < 1 ) {
 			$result['participant'] = $part->createCachedIterator( array() );
 			return $result;
 		}

 		$part_it = $part->getRegistry()->Query(
 				array(
 						new FilterAttributePredicate('SystemUser', $user_it->getId()),
 						new FilterAttributePredicate('Project', $this->project_it->getId())
 				)
 		);

 		if ( $part_it->getId() > 0 )
		{
			$project_roles = $part_it->getBaseRoles();
		}
		elseif ( !defined('PERMISSIONS_ENABLED') )
 		{
 			$role = new ProjectRole();
 			$role_it = $role->getRegistry()->Query(
 					array (
 							new FilterAttributePredicate('ReferenceName', 'lead'),
 							new FilterVpdPredicate($this->project_it->get('VPD'))
 					)
 				);

 			$part_id = $part->add_parms(
 					array (
 							'SystemUser' => $user_it->getId(),
 							'Project' => $this->project_it->getId(),
 							'IsActive' => 'Y',
 							'VPD' => $this->project_it->get('VPD'),
 							'Notification' => $this->project_it->getDefaultNotificationType()
 					)
 			);
 			$role = new ParticipantRole();
 			$role->add_parms(
 					array (
 							'Participant' => $part_id,
 							'Project' => $this->project_it->getId(),
 							'ProjectRole' => $role_it->getId(),
 							'VPD' => $this->project_it->get('VPD')
 					)
 			);
 			$part_it = $part->getExact($part_id);
 			$project_roles = $part_it->getBaseRoles();
 		}
		else
		{
			$part_it = $part->createCachedIterator( array ( 
				array( 'pm_ParticipantId' => GUEST_UID ) 
			));
			
			$linked_project_ids = array_filter(preg_split('/,/', $this->project_it->get('LinkedProject')), function($value) {
				return $value > 0;
			});
			if ( count($linked_project_ids) < 1 ) $linked_project_ids = array(0);

	 		$guest_it = $part->getRegistry()->Query(
	 				array(
	 						new FilterAttributePredicate('SystemUser', $user_it->getId()),
	 						new FilterAttributePredicate('Project', $linked_project_ids)
	 				)
	 		);
			$shared_access = count($linked_project_ids) > 0 ? $guest_it->count() > 0 : false;
            
			if ( $shared_access ) {
				$project_roles['linkedguest'] = true;
			}
			else {
				$project_roles['guest'] = true;
			}
		}
		
		$result['roles'] = $project_roles;
		$result['participant'] = $part_it;
		
		return $result;
 	}
 	
 	function getLanguageUid() 
 	{
 	    return $this->getProjectIt()->get('Language') == 2 ? 'EN' : 'RU';
 	}
 	
 	function getProjectIt() {
        return $this->project_it;
 	}

 	function getProjectObject() {
 	    return new Project();
    }

 	function setProjectIt( $project_it ) {
 		$this->project_it = $project_it;
 	}
 	
 	public function getLinkedIt()
 	{
 		if ( !is_object($this->linked_it) ) return getFactory()->getObject('Project')->getEmptyIterator();
 		
 	    $this->linked_it->moveFirst();
 	    
 	    return $this->linked_it;
 	}
 	
 	function getUserSettings()
 	{
 	    if ( is_object($this->settings) ) return $this->settings;
 	    
 	    $this->settings = new PMUserSettings( $this );
 	    
 	    return $this->settings;
 	}
 	
 	function setUserSettings( $settings )
 	{
 	    $this->settings = $settings;
 	}
 	
 	function getParticipantIt()
 	{
 	    return $this->part_it;
 	}
 	
 	function getSessionKey( & $project_it = null, & $user_it = null )
 	{
 		if ( !is_object($project_it) ) $project_it = $this->getProjectIt();
 		
 		$key = $project_it->getId();
 		
 		if ( !is_object($user_it) ) $user_it = $this->getUserIt();

 		if ( is_object($user_it) ) $key .= '-'.$user_it->getId();
 		
 		return 'session-pm-'.md5($key);
 	}
 	
 	function getCacheKey( & $project_it = null, & $user_it = null )
 	{
 		$key = 'pm';

 		if ( is_null($project_it) || $project_it === '' ) $project_it = $this->getProjectIt();
 		
 		if ( !is_object($user_it) ) $user_it = $this->getUserIt();
 		
 		if ( is_object($project_it) && $project_it->get('VPD') != '' ) {
 			$key .= '-'.$project_it->get('VPD');
 		}
		if ( is_string($project_it) && $project_it != '' ) {
			$key .= '-'.$project_it;
		}
 		
 		if ( is_object($user_it) && $user_it->getId() > 0 )
 		{
 			$key .= '-'.$user_it->getId();
 		}
 		
 		return $key;
 	}
 	
 	function truncate( $category = '' )
 	{
 	 	if ( $category != '' )
 		{
 			parent::truncate( $category );

 			return;
 		}
 	    
 		// reset project related cache

        $this->truncateForProject( $this->getProjectIt() );
 		
 		// reset cached values for the current user
 		
		$cache_key = $this->getCacheKey( $project_it, $this->getUserIt() );
			
		parent::truncate( $cache_key );
			
 	}
 	
 	function truncateForProject( $project_it )
 	{
 	    global $model_factory;
 	    
 		$this->truncate( 'pm-'.$project_it->get('VPD') );
 		
 		// reset users related cache
 		
 		$user = $model_factory->getObject('cms_User');
 	    
 		$user_it = $user->getAll();
 			
 		while( !$user_it->end() )
 		{
 		    $this->truncateFor( $project_it, $user_it );
 			
 			$user_it->moveNext();
 		}
 		
 		// reset linked projects cache
 		
 		$linked_it = $project_it->getRef('LinkedProject');
 		
 		while ( !$linked_it->end() )
 		{
 		    $this->truncate( 'pm-'.$linked_it->get('VPD') );
 		    
 		    $user_it->moveFirst();
 		    
 		 	while( !$user_it->end() )
     		{
     		    $this->truncateFor( $linked_it, $user_it );
     			
     			$user_it->moveNext();
     		}
 		    
 		    $linked_it->moveNext();
 		}
 	}
 	
 	function truncateFor( $project_it, $user_it )
 	{
 	    $cache_key = $this->getCacheKey( $project_it, $user_it );
 	    
 	    $this->truncate( $cache_key );
 	    	
 	    $session_key = $this->getSessionKey( $project_it, $user_it );

 	    $this->set($session_key , '', 'usr' );
 	}
 
 	function getRoles()
 	{
 		return $this->project_roles;
 	}
 	
 	function getApplicationUrl( $object = null )
 	{
 	    global $model_factory;
 	    
 	    if ( !is_a($object, 'Metaobject') && !is_a($object, 'OrderedIterator') )
 	    {
 	        return '/pm/'.$this->getProjectIt()->get('CodeName').'/';
 	    }

 	    $vpd_context = is_a($object, 'Metaobject') ? $object->getVpdContext() : $object->get('VPD');
 	    
 	    if ( $vpd_context == '' || $this->getProjectIt()->get('VPD') == $vpd_context )
 	    {
 	        return '/pm/'.$this->getProjectIt()->get('CodeName').'/';
 	    }
 	    
 	    $linked_it = $this->getLinkedIt();

 	    $linked_it->moveTo('VPD', $vpd_context);

 	    if ( $linked_it->getId() != '' )
 	    {
 	        return '/pm/'.$linked_it->get('CodeName').'/';
 	    }

 	    $project_it = $model_factory->getObject('pm_Project')->getByRef('VPD', $vpd_context);

 	 	if ( $project_it->getId() != '' )
 	    {
 	        return '/pm/'.$project_it->get('CodeName').'/';
 	    }
 	    
 	    return '/pm/'.$this->getProjectIt()->get('CodeName').'/';
 	}
 	
 	function getSite()
 	{
 	    return 'pm';
 	}
}
