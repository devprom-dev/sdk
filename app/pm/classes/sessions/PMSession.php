<?php
 
include_once SERVER_ROOT_PATH.'core/c_session.php';
include_once SERVER_ROOT_PATH."co/classes/ResourceBuilderCoLanguageFile.php";

include SERVER_ROOT_PATH.'pm/classes/model/ModelFactoryProject.php';
include SERVER_ROOT_PATH.'pm/classes/model/permissions/AccessPolicyProject.php';

include SERVER_ROOT_PATH.'pm/classes/widgets/ModuleCategoryBuilderCommon.php';
include SERVER_ROOT_PATH.'pm/classes/common/PMContextResourceBuilder.php';

include SERVER_ROOT_PATH.'pm/classes/common/PMUserSettings.php'; 
include SERVER_ROOT_PATH."pm/classes/common/ModuleProjectsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsTasksBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SharedObjectsPlanBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ObjectMetadataCustomAttributesBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ObjectModelCustomAttributesBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/SearchableObjectsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/ChangeLogEntitiesProjectBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/permissions/AccessRightEntitySetCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilderTask.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilderRequest.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilderIssue.php";
include SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilderTask.php";
include SERVER_ROOT_PATH."pm/classes/workflow/events/ApplyBusinessActionsEventHandler.php";
include SERVER_ROOT_PATH."pm/classes/workflow/events/ResetFieldsEventHandler.php";

include SERVER_ROOT_PATH."pm/classes/settings/DictionaryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/settings/WorkflowBuilderCommon.php";

include SERVER_ROOT_PATH."pm/views/common/PageSettingSet.php";
include SERVER_ROOT_PATH.'pm/views/common/PageSettingCommonBuilder.php';
include SERVER_ROOT_PATH."pm/classes/settings/EstimationStrategyCommonBuilder.php";

include SERVER_ROOT_PATH."pm/classes/report/ReportsCommonBuilder.php";
include SERVER_ROOT_PATH."pm/classes/report/events/CustomReportModelEventsHandler.php";

include SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/issues/RequestMetadataPermissionsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/RequestTriggersCommon.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/IssueOrderNumTrigger.php";
include SERVER_ROOT_PATH."pm/classes/issues/triggers/IssueModifyProjectTrigger.php";
include SERVER_ROOT_PATH."pm/classes/issues/VersionedObjectRegistryBuilderIssue.php";
include SERVER_ROOT_PATH."pm/classes/issues/events/ResetTasksEventHandler.php";

include SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/tasks/TaskMetadataPermissionsBuilder.php";
include SERVER_ROOT_PATH."pm/classes/tasks/triggers/TaskOrderNumTrigger.php";
include SERVER_ROOT_PATH."pm/classes/tasks/TaskTypeMetadataBuilder.php";

include SERVER_ROOT_PATH."pm/classes/plan/IterationMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/plan/ReleaseMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/plan/MilestoneMetadataBuilder.php";

include SERVER_ROOT_PATH."pm/classes/product/FeatureMetadataBuilder.php";
include SERVER_ROOT_PATH."pm/classes/common/HistoricalObjectsRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilderLatest.php";

include SERVER_ROOT_PATH."pm/classes/common/triggers/CacheSessionProjectTrigger.php";
include SERVER_ROOT_PATH."pm/classes/communications/triggers/DeleteCommentsTrigger.php";

include_once SERVER_ROOT_PATH."pm/classes/notificators/PMEmailNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/notificators/PMChangeLogNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetWorkItemDatesTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetPlanItemDatesTrigger.php";

include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiPageNewVersionTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/triggers/WikiBreakTraceTrigger.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageMetadataBuilder.php";

///////////////////////////////////////////////////////////////////////
class PMSession extends SessionBase
{
 	var $part_it;
 	var $project_roles;
 	var $project_it;
 	var $linked_it;
 	var $shareable;
 	var $module;
 	var $language;
 	
 	private $shared_vpds = array();
 	
 	private $project_info = null;
 	
 	function __construct( $project_info, $factory = null, $builders = null, $cache_service = null )
 	{
 		$this->project_info = $project_info;
 		
        parent::__construct( $factory, $builders, $cache_service );
 	}
 	
 	public function configure()
 	{
 		global $model_factory, $plugins;
 		
        $this->setup($this->project_info);
 		
        // destroy services
        $model_factory->setAccessPolicy(null);
        
        $model_factory->setEntityOriginationService(null);
        
        $origination_service = $this->buildOriginationService(getCacheService());
        $origination_service->getCacheService()->setDefaultPath('pm-'.$this->getProjectIt()->get('VPD'));
        
        // reconfigure the cache
 		$this->setCacheEngine(getCacheService());
 		
 		$this->getCacheEngine()->setDefaultPath($this->getCacheKey());
        
 		// create the new model factory
 		$model_factory = new ModelFactoryProject( 
 				$plugins, 
 				$this->getCacheEngine(), 
 				$this->buildAccessPolicy($this->getCacheEngine()),
 				null,
 				$origination_service
 		);

        parent::configure();
 		
        $this->addBuilder(new ProjectTemplateSectionsRegistryBuilderLatest($this));
        
        $this->user_it = null;
        
 		getLanguage();
 	}
 	
 	public function & buildAccessPolicy( $cache_service )
 	{
 		return new AccessPolicyProject( $cache_service, $this );
 	}
 	
 	public function & buildOriginationService( $cache_service )
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
 	                    new TaskMetadataPermissionsBuilder(),
 	                    new IterationMetadataBuilder(),
 	                    new ReleaseMetadataBuilder(),
 	                    new MilestoneMetadataBuilder(),
 	                    new RequestMetadataPermissionsBuilder(),
 	                    new PMChangeLogNotificator(),
 	                    new PMEmailNotificator(),
 	                    new RequestTriggersCommon(),
 	                    new IssueOrderNumTrigger(),
 	                    new TaskOrderNumTrigger(),
 	                    new SetWorkItemDatesTrigger(),
 	            		new SetPlanItemDatesTrigger(),
 	                    new DeleteCommentsTrigger(),
 	                    new IssueModifyProjectTrigger(),
 	                    new FeatureMetadataBuilder(),
 	            		new StateBusinessActionBuilderTask(),
 	                    new StateBusinessActionBuilderRequest(),
 	                    new StateBusinessRuleBuilderIssue(),
 	                    new StateBusinessRuleBuilderTask(),
 	            		new DictionaryBuilderCommon($this),
 	            		new WorkflowBuilderCommon($this),
 	            		new HistoricalObjectsRegistryBuilderCommon(),
 	            		new ProjectTemplateSectionsRegistryBuilderCommon($this),
 	            		new VersionedObjectRegistryBuilderIssue(),
 	            		
 	            		// widgets
 	            		new ModuleCategoryBuilderCommon(),
 	            		
 	            		// triggers
 	            		new WikiPageNewVersionTrigger(),
 	            		new WikiBreakTraceTrigger(),
 	            		new CustomReportModelEventsHandler()
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
 	            		
 	            		// model
 	            		new ResetFieldsEventHandler(),
 	            		new ApplyBusinessActionsEventHandler(),
 	            		new ResetTasksEventHandler()
 	            )
 	    );
 	}
 	
 	function setup( $project_info )
 	{
 		global $part_it, $project_it;
 		
 		$this->initialize( $project_info );
 		
 		$part_it = $this->part_it;
 		$project_it = $this->project_it;
 	}
 	
 	private function initialize( $project_info )
 	{
 		global $model_factory;
 		
 		$this->project_it = $this->findProject($project_info);

 		$data = $this->get($this->getSessionKey($this->project_it), 'usr');
 		
 		if ( is_array($data) )
 		{
 			$part = new Participant();
 			
 			$this->part_it = $part->createCachedIterator( $data['participant'] );
 			
 			$this->project_roles = $data['roles'];
 			
 			$this->project_it->setRowset( $data['project'] );
 			
 			$this->linked_it = $model_factory->getObject('pm_Project')->createCachedIterator( $data['linked'] );

 			$link = $model_factory->getObject('pm_ProjectLink');
 			
 			$this->resetCaches();
 			
 			return;
 		}
 		
 		$project_data = $this->buildProjectData( $this->project_it );
 		
 		$this->project_it = $project_data['project']; 
 		$this->linked_it = $project_data['linked']; 
 		
 		$this->resetCaches();
 		
 		$roles_data = $this->buildParticipantData();
 		
 		$this->part_it = $roles_data['participant']; 
 		$this->project_roles = $roles_data['roles']; 
 		
		$cached_session = array( 
			'participant' => is_object($this->part_it) ? $this->part_it->getRowset() : array(),
			'roles' => $this->project_roles,
			'project' => is_object($this->project_it) ? $this->project_it->getRowset() : array(),
			'linked' => is_object($this->linked_it) ? $this->linked_it->getRowset() : array(),
		);

		$this->set($this->getSessionKey(), $cached_session, 'usr');
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
 	
 	protected function buildProjectData( & $project_it )
 	{
 		$object_it = $this->getProjectIt();
 		
 		if ( is_object($object_it) && $project_it->getId() != $object_it->getId() )
 		{
			getFactory()->resetCache();
 		}

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
 		
 		if ( !is_object($user_it) )
 		{
 			$result['participant'] = $part->createCachedIterator( array() );
 			 
 			return $result;
 		}

 		if ( $user_it->getId() < 1 )
 		{
 			$result['participant'] = $part->createCachedIterator( array() );
 			 
 			return $result;
 		}

 		$part_it = $part->getRegistry()->Query(
 				array(
 						new FilterAttributePredicate('SystemUser', $user_it->getId()),
 						new FilterAttributePredicate('Project', $this->project_it->getId()),
 						new FilterAttributePredicate('IsActive', 'Y')
 				)
 		);

 		if ( $part_it->getId() > 0 )
		{
			$project_roles = $part_it->getBaseRoles();
		}
		else
		{
			$part_it = $part->createCachedIterator( array ( 
				array( 'pm_ParticipantId' => GUEST_UID ) 
			));
			
			$linked_project_ids = preg_split('/,/', $this->project_it->get('LinkedProject'));

	 		$guest_it = $part->getRegistry()->Query(
	 				array(
	 						new FilterAttributePredicate('SystemUser', $user_it->getId()),
	 						new FilterAttributePredicate('Project', $linked_project_ids),
	 						new FilterAttributePredicate('IsActive', 'Y')
	 				)
	 		);
			
			$shared_access = count($linked_project_ids) > 0 ? $guest_it->count() > 0 : false;
            
			$project_roles['guest'] = true;
			
			if ( $shared_access ) $project_roles['linkedguest'] = true;
		}
		
		$result['roles'] = $project_roles;
		$result['participant'] = $part_it;
		
		return $result;
 	}
 	
 	function getLanguageUid() 
 	{
 	    return $this->getProjectIt()->get('Language') == 2 ? 'EN' : 'RU';
 	}
 	
 	function getLanguage() 
 	{
 	    global $model_factory;

 	    if ( is_object($this->language) ) return $this->language;
 	    
    	$this->language = $this->getLanguageUid() == 'EN' ? new LanguageEnglish() : new Language();
         
        $this->language->Initialize( $model_factory->getObject('CustomResource') );
		
		return $this->language; 
 	}
 	
 	function getProjectIt()
 	{
 		return $this->project_it;
 	}
 	
 	function setProjectIt( $project_it )
 	{
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
 	
 	function setParticipantIt( $participant_it )
 	{
 	    $this->open( $participant_it->getRef('SystemUser') );
 	    
 	    $this->part_it = $participant_it;
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

 		if ( !is_object($project_it) ) $project_it = $this->getProjectIt();
 		
 		if ( !is_object($user_it) ) $user_it = $this->getUserIt();
 		
 		if ( is_object($project_it) && $project_it->get('VPD') != '' )
 		{
 			$key .= '-'.$project_it->get('VPD');
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
