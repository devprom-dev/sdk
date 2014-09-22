<?php

include_once "ResourceRegistry.php";

class Resource extends MetaobjectCacheable
{
 	function __construct( ResourceRegistry $registry = null ) 
 	{
 		parent::__construct('cms_Resource', is_object($registry) ? $registry : new ResourceRegistry($this));
 		
 		$this->setAttributeVisible( 'OrderNum', false );
 	}

 	function getDisplayName()
 	{
 		return text(940);
 	}
}