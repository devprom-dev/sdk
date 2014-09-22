<?php

include "AttributeGroupRegistry.php";
	
class AttributeGroup extends MetaobjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new AttributeGroupRegistry() );
	}
}