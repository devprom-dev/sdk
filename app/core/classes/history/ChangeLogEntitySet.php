<?php

include_once "ChangeLogEntityRegistry.php";
include_once "ChangeLogEntitiesBuilder.php";

class ChangeLogEntitySet extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('entity', new ChangeLogEntityRegistry($this));
 	}
}
