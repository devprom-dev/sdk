<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include_once "CustomAttributeTypeIterator.php";
include_once "CustomAttributeTypeRegistry.php";

class CustomAttributeType extends MetaobjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new CustomAttributeTypeRegistry($this));
	}
	
	public function createIterator()
	{
		return new CustomAttributeTypeIterator($this);
	}
}