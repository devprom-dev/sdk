<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "AutoActionEventRegistry.php";

class AutoActionEvent extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new AutoActionEventRegistry($this));
 	}
}
