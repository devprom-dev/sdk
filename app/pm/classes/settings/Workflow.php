<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "WorkflowRegistry.php";

class Workflow extends PMObjectCacheable
{	
	public function __construct()
	{
		parent::__construct('entity', new WorkflowRegistry($this));
	}
}
