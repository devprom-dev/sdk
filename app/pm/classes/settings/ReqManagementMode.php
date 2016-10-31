<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "ReqManagementModeRegistry.php";

class ReqManagementMode extends PMObjectCacheable
{
	public function __construct() {
		parent::__construct('entity', new ReqManagementModeRegistry());
	}
}