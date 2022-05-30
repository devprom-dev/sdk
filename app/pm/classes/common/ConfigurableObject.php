<?php
include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "CustomizableObjectRegistry.php";
include_once "CustomizableObjectIterator.php";

class ConfigurableObject extends PMObjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new CustomizableObjectRegistry($this));
 	}
 	
	function createIterator() {
		return new CustomizableObjectIterator($this);
	}
}
