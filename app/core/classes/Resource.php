<?php

include_once "ResourceRegistry.php";

class Resource extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		parent::__construct('cms_Resource', is_object($registry) ? $registry : new ResourceRegistry($this));
 		
 		$this->setAttributeVisible( 'OrderNum', false );
 	}
}