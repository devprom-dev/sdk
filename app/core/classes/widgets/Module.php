<?php

include 'ModuleIterator.php';
include "ModuleRegistry.php";

class Module extends MetaobjectCacheable
{
 	function __construct()
 	{
 		parent::__construct('cms_PluginModule', new ModuleRegistry($this));
 	}

 	function createIterator()
 	{
 		return new ModuleIterator( $this );
 	}

	function getCacheCategory()
	{
		// participant-wide cache
	    return $this->getMetadataCacheName();
	}
}

