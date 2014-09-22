<?php

include "WikiTypeRegistry.php";

class WikiType extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new WikiTypeRegistry($this));
 	}
}
