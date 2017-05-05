<?php
include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include_once "IntegrationTrackerRegistry.php";

class IntegrationTracker extends PMObjectCacheable
{
	public function __construct( $registry = null ) {
		parent::__construct('entity', new IntegrationTrackerRegistry($this));
	}
}