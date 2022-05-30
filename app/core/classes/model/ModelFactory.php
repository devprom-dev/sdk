<?php
include "ObjectHierarchyIterator.php";
include "sorts/SortObjectHierarchyClause.php";
include "predicates/ObjectRootFilter.php";
include SERVER_ROOT_PATH.'cms/classes/model/ModelFactoryBase.php';
include SERVER_ROOT_PATH.'core/classes/model/classes.php';
include_once "ModelEntityOriginationService.php";
include_once "ModelProjectOriginationService.php";
include 'validation/ModelValidator.php';
include 'mappers/ModelMapper.php';
include 'mappers/ModelDataTypeMappingPositives.php';
include 'mappers/ModelDefaultValuesMapping.php';
include 'mappers/ModelPasswordMapping.php';

class ModelFactory extends ModelFactoryBase
{
    private $classes = array();
	private $plugins = null;
	private $logger;
 	
 	private $origination_service = null;
 	
 	function __construct($pluginsManager, $cache_engine, $cache_key = 'global', $access_policy = null, $events_manager = null, $origination_service = null )
 	{
        global $model_factory;
        $model_factory = $this;

 	    parent::__construct($cache_engine, $cache_key, $access_policy, $events_manager);

		$this->plugins = $pluginsManager;
		$this->classes = $this->buildClasses();
 	    $this->origination_service = is_object($origination_service)
 	    		? $origination_service : new ModelEntityOriginationService($this->getCacheService(), $cache_key);

 	    if ( is_object($pluginsManager) ) $pluginsManager->checkLicenses();
 	}

 	function __sleep() {
        return array();
    }

    public function getPluginsManager() {
		return $this->plugins;
	}

 	public function getEntityOriginationService()
 	{
 		return $this->origination_service;
 	}
 	
 	public function setEntityOriginationService( $service )
 	{
 		$this->origination_service = $service;
 	}

    public function setCacheKey( $key ) {
        parent::setCacheKey($key);
        $this->origination_service->setCacheKey($key);
    }

	protected function buildClasses()
	{
		return array(
			'cms_checkquestion' => array( 'CheckQuestion' ),
			'cms_snapshot' => array('Snapshot'),
			'cms_snapshotitem' => array('SnapshotItem'),
		    'cms_snapshotitemvalue' => array('SnapshotItemValue'),
			'cms_systemsettings' => array('SystemSettings'),
			'pm_project' => array( 'Project'),
			'pm_projecttemplate' => array( 'ProjectTemplate' ),
			'program' => array( 'Program'),
			'objectchangelog' =>  array( 'ChangeLog'),
			'co_scheduledjob' => array( 'CoScheduledJob' ),
			'cms_update' => array( 'Update' ),
			'cms_user' => array( 'User' ),
		    'cms_language' => array( 'LanguageEntity' ),
			'cms_pluginmodule' => array( 'Module'),
			'cms_resource' => array( 'Resource' ),
			'pm_calendarinterval' => array( 'Calendar' ),
			'pm_customattribute'  => array('PMCustomAttribute'),
            'pm_attributevalue'  => array('PMCustomAttributeValue'),
			'cms_tempfile' => array( 'TempFile' ),
            'pm_tasktype' => array('TaskTypeBase'),
            'pm_projectrole' => array('ProjectRoleBase'),
            'emailqueue' => array('EmailQueue')
		);
	}
 	
 	function getClass( $class_name )
 	{
		$class = $this->classes[strtolower($class_name)];

		if ( is_array($class) )
		{
			$class_name = $class[0];
			
			if ( !class_exists($class_name, false) && $class[1] != '' )
			{
			    $path = $class[2].$class[1];
			    
			    if ( strpos($path, SERVER_ROOT_PATH) === false ) $path = SERVER_ROOT_PATH.$path;
			    
			    include( $path );
			}
		}

		if ( strtolower($class_name) == 'metaobject' ) {
		    return '';
        }

		return $class_name;
	}
 	
	function getObject($class_name) 
	{
		$use_class = $this->getClass( $class_name );

		if ( $use_class == '' ) return null;
		
		return parent::getObject( $use_class );
	}
	
	function getObject2( $class_name, $parms ) 
	{
		$use_class = $this->getClass( $class_name );

		if ( $use_class == '' ) return null;

		return parent::getObject2( $use_class, $parms );
	}
	
	function getLogger()
	{
 		try 
 		{
 			if ( !is_object($this->logger) )
 			{
 				$this->logger = Logger::getLogger('System');
 			}
 			
 			return $this->logger;
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
	}
	
	function error( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->error( $message );
	}
	
	function debug( $message )
	{
		if ( defined('DEBUG_ENABLED') && DEBUG_ENABLED ) {
			$log = $this->getLogger();
			if ( !is_object($log) ) return;
			$log->debug( $message );
		}
	}
	
	function info( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->info( $message );
	}
	
	public static function get()
	{
    	global $model_factory;
    
    	if ( !is_object($model_factory) ) {
    		$model_factory = new ModelFactory(PluginsFactory::Instance(), getCacheService());
    	}
    	return $model_factory;
	}

	protected function getModelValidators() {
        return array (
            new \ModelValidatorTypes(),
            new \ModelValidatorObligatory()
        );
    }

    protected function getModelMappers() {
 	    return array(
 	        new \ModelDataTypeMappingPositives(),
            new \ModelDefaultValuesMapping(),
            new \ModelPasswordMapping()
        );
    }

	public function transformEntityData( $object, &$parms, $data = array(), $validators = array(), $mappers = array() )
    {
        $mapper = new \ModelMapper(
            array_merge(
                array(
                    new \ModelDataTypeMapper() // convert data into database format
                ),
                $object->getMappers(),
                $this->getModelMappers()
            )
        );
        $mapper->map($object, $parms);

        $validator = new \ModelValidator(
            array_merge(
                $validators,
                $object->getValidators(),
                $this->getModelValidators()
            )
        );

        // validate field values
        $data = array_merge($data, $parms);
        $message = $validator->validate( $object, $data );
        if ( $message != '' ) throw new \Exception($message);
    }

	public function createEntity( $object, $parms, $validators = array(), $mappers = array() )
    {
        if ( !$this->getAccessPolicy()->can_create($object) ) {
            throw new \Exception("There is no permission to create entity of class " . get_class($object));
        }

        $alternativeKeyAttributes = $object->getAttributesByGroup('alternative-key');
        if ( count($alternativeKeyAttributes) > 0 ) {
            $validators[] = new ModelValidatorUnique($alternativeKeyAttributes);
        }

        $this->transformEntityData($object, $parms, array(), $validators, $mappers);

        $id = $object->add_parms($parms);
        if ( $id < 1 ) throw new \Exception("Unable create entity of class " . get_class($object));

        $objectIt = $object->getExact($id);
        $this->notifyWorkflowChanges($objectIt);

        return $objectIt;
    }

    public function mergeEntity( $object, $parms, $validators = array(), $mappers = array() )
    {
        if ( !$this->getAccessPolicy()->can_create($object) ) {
            throw new \Exception("There is no permission to merge entity of class " . get_class($object));
        }

        $this->transformEntityData($object, $parms, array(), $validators, $mappers);

        $alternativeKeyAttributes = $object->getAttributesByGroup('alternative-key');
        foreach( $alternativeKeyAttributes as $index => $key ) {
            if ( $parms[$key] == '' ) unset($alternativeKeyAttributes[$index]);
        }
        if ( count($alternativeKeyAttributes) > 0 )
        {
            $modifiedIt = $object->getRegistry()->Merge($parms, $alternativeKeyAttributes);
            if ( $parms['Transition'] != '' || $parms['State'] != '' ) {
                $this->notifyWorkflowChanges($modifiedIt);
            }
            return $modifiedIt;
        }
        else {
            $objectIt = $object->getRegistry()->Create($parms);
            $this->notifyWorkflowChanges($objectIt);
            return $objectIt;
        }
    }

    public function modifyEntity( $objectIt, $parms, $validators = array(), $mappers = array() )
    {
        if ( $parms['Transition'] != '' && $objectIt->object instanceof \MetaobjectStatable ) {
            if ( !$this->getAccessPolicy()->can_modify_attribute($objectIt->object, 'State') ) {
                throw new \Exception("There is no permission to modify entity of class " . get_class($objectIt->object));
            }
        }
        else if ( !$this->getAccessPolicy()->can_modify($objectIt) ) {
            throw new \Exception("There is no permission to modify entity of class " . get_class($objectIt->object));
        }

        $alternativeKeyAttributes = $objectIt->object->getAttributesByGroup('alternative-key');
        if ( count($alternativeKeyAttributes) > 0 ) {
            $parms[$objectIt->object->getIdAttribute()] = $objectIt->getId();
            $validators[] = new ModelValidatorUnique($alternativeKeyAttributes);
        }

        $this->transformEntityData($objectIt->object, $parms, $objectIt->getData(), $validators, $mappers);

        if ( $objectIt->object->modify_parms($objectIt->getId(), $parms) < 1 ) {
            throw new \Exception("Unable modify entity of class " . get_class($objectIt->object) . ' id ' . $objectIt->getId());
        }

        getFactory()->resetCache();
        $modifiedIt = $objectIt->object->getExact($objectIt->getId());

        if ( $parms['Transition'] != '' || $objectIt->get('State') != $modifiedIt->get('State') ) {
            $this->notifyWorkflowChanges($modifiedIt);
        }

        return $modifiedIt;
    }

    public function getEntities( $object, $objectId ) {
        return $object->getExact($objectId);
    }

    protected function notifyWorkflowChanges( $objectIt )
    {
        $data = array();
        foreach( $objectIt->getData() as $key => $value ) {
            if ( $objectIt->object->IsAttributeVisible($key) ) {
                $data[$key] = $value;
            }
        }
        getFactory()->getEventsManager()
            ->executeEventsAfterBusinessTransaction(
                $objectIt->copy(), 'WorklfowMovementEventHandler', $data
            );
    }
}

function getFactory()
{
    return ModelFactory::get();
}