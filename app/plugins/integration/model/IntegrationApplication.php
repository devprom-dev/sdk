<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "IntegrationApplicationRegistry.php";

class IntegrationApplication extends PMObjectCacheable
{
	public function __construct( $registry = null )
	{
		parent::__construct('entity', new IntegrationApplicationRegistry($this));
	}
}