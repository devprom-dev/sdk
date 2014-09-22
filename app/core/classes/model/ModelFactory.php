<?php

include SERVER_ROOT_PATH.'cms/classes/model/ModelFactoryBase.php';
include_once "ModelEntityOriginationService.php";
include_once "ModelProjectOriginationService.php";
include "classes.php";

class ModelFactory extends ModelFactoryBase
{
 	var $classes = array();
 	
 	var $logger;
 	
 	private $origination_service = null;
 	
 	function __construct($cache_engine = null, $access_policy = null, $events_manager = null, $origination_service = null )
 	{
 	    parent::__construct($cache_engine, $access_policy, $events_manager);
 	    
 	    $this->classes = $this->buildClasses();
 	    
 	    $this->origination_service = is_object($origination_service) 
 	    		? $origination_service : new ModelEntityOriginationService($this->getCacheService());
 	}
 	
 	public function getEntityOriginationService()
 	{
 		return $this->origination_service;
 	}
 	
 	public function setEntityOriginationService( $service )
 	{
 		$this->origination_service = $service;
 	}
 	
	protected function buildClasses()
	{
		return array(
			'cms_checkquestion' => array( 'CheckQuestion' ),
			'cms_snapshot' => array('Snapshot'),
			'cms_snapshotitem' => array('SnapshotItem'),
		    'cms_snapshotitemvalue' => array('SnapshotItemValue'),
		    'cms_idshash' => array('HashIds'),
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
			'pm_customattribute'  => array('PMCustomAttribute')
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
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->debug( $message );
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
    
    	if ( !is_object($model_factory) ) 
    	{
    		$model_factory = new ModelFactory;
    	}
    	
    	return $model_factory;
	}
}

////////////////////////////////////////////////////////////////////////////////
function &getModelFactory()
{
    return ModelFactory::get();
}

function &getFactory()
{
    return ModelFactory::get();
}