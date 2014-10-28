<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "PMCustomDictionaryRegistry.php";

class PMCustomDictionary extends CacheableSet
{
 	function __construct( $object, $attribute ) 
 	{
 		$this->object = $object;
 		$this->attribute = $attribute;
 		
 	    parent::__construct( new PMCustomDictionaryRegistry($this) );
 	}
 	
	function getCacheCategory()
	{
	    return getSession()->getCacheKey().get_class($this->object).$this->attribute;
	}
	
	function getAttributeObject()
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
