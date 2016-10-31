<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "TransitionReasonTypeRegistry.php";

class TransitionReasonType extends PMObjectCacheable
{
	public function __construct() {
		parent::__construct('entity', new TransitionReasonTypeRegistry());
	}
}