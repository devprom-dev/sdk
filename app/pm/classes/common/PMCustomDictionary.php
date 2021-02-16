<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "PMCustomDictionaryRegistry.php";

class PMCustomDictionary extends CacheableSet
{
 	function __construct( $object = null, $attribute = null )
 	{
 		$this->object = $object;
 		$this->attribute = $attribute;
 		
 	    parent::__construct( new PMCustomDictionaryRegistry($this) );
 	}
 	
	function getCacheCategory()
	{
	    return getSession()->getCacheKey().get_class($this->object).$this->attribute;
	}
	
	function getObjectForAttribute()
	{
		return $this->object;
	}
	
	function getAttribute()
	{
		return $this->attribute;
	}
	
	private $object = null;
	private $attribute = null;
}
