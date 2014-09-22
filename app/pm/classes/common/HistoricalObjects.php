<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include_once "HistoricalObjectsRegistry.php";

class HistoricalObjects extends CacheableSet
{
 	public function __construct() 
 	{
 		parent::__construct( new HistoricalObjectsRegistry($this) );
 	}
}
