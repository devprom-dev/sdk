<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "MethodologyPlanningModeRegistry.php";

class MethodologyPlanningMode extends PMObjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new MethodologyPlanningModeRegistry());
	}
}