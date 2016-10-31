<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "StateCommonRegistry.php";

class StateCommon extends PMObjectCacheable
{
	public function __construct() {
		parent::__construct('entity', new StateCommonRegistry());
	}
}