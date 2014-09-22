<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include_once "DictionaryRegistry.php";

class Dictionary extends CacheableSet
{
	public function __construct()
	{
		parent::__construct(new DictionaryRegistry($this));
	}
}