<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "PMCustomDictionaryRegistry.php";

class PMCustomDictionary extends CacheableSet
{
 	function __construct() 
 	{
 	    parent::__construct( new PMCustomDictionaryRegistry($this) );
 	}
 	
	function getCacheCategory()
	{
	    return getSession()->getCacheKey().get_class($this->object).$this->attribute;
	}
}
