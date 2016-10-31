<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "SearchableObjectRegistry.php";

class SearchableObjectSet extends CacheableSet
{
	function __construct()
	{
		return parent::__construct( new SearchableObjectRegistry($this) );
	}
}
