<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "CustomizableObjectRegistry.php";

class CustomizableObjectSet extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new CustomizableObjectRegistry($this));
 	}
}
