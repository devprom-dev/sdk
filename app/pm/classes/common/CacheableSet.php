<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";

class CacheableSet extends PMObjectCacheable
{
 	function __construct( $registry = null )
 	{
 		parent::__construct('entity', is_object($registry) ? $registry : new ObjectRegistrySQL($this) );
 	}
 	
	function getCacheCategory()
	{
		// participant-wide cache
	    return $this->getMetadataCacheName();
	}
}
