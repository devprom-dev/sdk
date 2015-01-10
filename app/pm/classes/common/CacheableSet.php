<?php

class CacheableSet extends MetaobjectCacheable
{
 	function __construct( $registry = null )
 	{
 		parent::__construct('entity', is_object($registry) ? $registry : new ObjectRegistrySQL($this) );
 	}
 	
 	function getVpds()
 	{
 		return array();
 	}
 	
	function getCacheCategory()
	{
		// participant-wide cache
	    return getSession()->getCacheKey();
	}
}
