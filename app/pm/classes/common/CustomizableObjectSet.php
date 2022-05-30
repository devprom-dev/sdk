<?php
include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "CustomizableObjectRegistry.php";
include_once "CustomizableObjectIterator.php";

class CustomizableObjectSet extends PMObjectCacheable
{
 	function __construct() 
 	{
 	    $registry = new CustomizableObjectRegistry($this);
        $registry->useTypes();
 		parent::__construct('entity', $registry);
 	}
 	
 	function checkObject( $object ) {
 		return in_array(strtolower(get_class($object)), $this->getAll()->fieldToArray('ReferenceName'));
 	}

	function createIterator() {
		return new CustomizableObjectIterator($this);
	}
}
