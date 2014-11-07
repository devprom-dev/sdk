<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "WikiTypeRegistry.php";

class WikiType extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new WikiTypeRegistry($this));
 	}
}
