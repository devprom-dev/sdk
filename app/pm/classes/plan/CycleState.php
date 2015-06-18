<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "CycleStateRegistry.php";

class CycleState extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new CycleStateRegistry($this));
 	}
 	
 	function getDisplayName()
 	{
 		return translate('Состояние');
 	}
}