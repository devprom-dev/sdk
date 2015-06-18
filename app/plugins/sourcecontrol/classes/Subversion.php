<?php

include "SubversionIterator.php";
include "SCMConnector.php";
include "predicates/RepositoryActivePredicate.php";

class Subversion extends Metaobject
{
 	private $connectors = array();
 	
 	function __construct() 
 	{
 		parent::__construct('pm_Subversion');
		
 		$this->defaultsort = 'RecordCreated DESC';
 	}
 	
 	function createIterator() 
 	{
 		return new SubversionIterator( $this );
 	}
 	
 	function getDisplayName()
 	{
 		return text('sourcecontrol2');
 	}
 	
	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'module/sourcecontrol/connection?';
	}
 	
 	function addConnector( $connector )
 	{
 		if ( array_key_exists( strtolower(get_class($connector)), $this->connectors ) ) return;
 		$this->connectors[strtolower(get_class($connector))] = $connector;
 	}
 	
 	function getConnector( $class_name = '' )
 	{
		return $this->connectors[strtolower($class_name)];
 	}
 	
 	function & getConnectors()
 	{
 		return $this->connectors;
 	}
 	
	function getDefaultAttributeValue( $name )
	{
		global $project_it;
		
		switch ( $name )
		{
			case 'Project':
				return $project_it->getId();
				
			default:
				return parent::getDefaultAttributeValue($name);
		}
	}
	
	function modify_parms ( $id, $parms )
	{
		$connector = $this->getConnector( $parms['ConnectorClass'] );
		
		list( $parms['SVNPath'], $parms['RootPath'] ) = 
			$connector->transformUrl( $parms['SVNPath'], $parms['RootPath'] );
		
		$result = parent::modify_parms ( $id, $parms );
		
		if ( $result < 1 ) return $result;
		
		$object_it = $this->getExact( $id );
		
		$object_it->getConnector()->resetCredentials();
		
		return $id;
	}
	
	function add_parms ( $parms )
	{
		global $model_factory;
		
		$connector = $this->getConnector( $parms['ConnectorClass'] );
		
		list( $parms['SVNPath'], $parms['RootPath'] ) = 
			$connector->transformUrl( $parms['SVNPath'], $parms['RootPath'] );
		
		return parent::add_parms( $parms );
	}
}
