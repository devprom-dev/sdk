<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "IntegrationTypeRegistry.php";

class IntegrationType extends PMObjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new IntegrationTypeRegistry($this));
	}
}