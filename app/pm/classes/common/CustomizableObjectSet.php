<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "CustomizableObjectRegistry.php";

class CustomizableObjectSet extends PMObjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('entity', new CustomizableObjectRegistry($this));
 	}
 	
 	function checkObject( $object )
 	{
 		return in_array(strtolower(get_class($object)), $this->getAll()->fieldToArray('ReferenceName'));
 	}
}
