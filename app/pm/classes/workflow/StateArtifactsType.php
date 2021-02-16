<?php
include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "StateArtifactsTypeRegistry.php";

class StateArtifactsType extends PMObjectCacheable
{
	public function __construct() {
		parent::__construct('entity', new StateArtifactsTypeRegistry());
	}
}
