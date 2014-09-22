<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "WorkflowRegistry.php";

class Workflow extends CacheableSet
{	
	public function __construct()
	{
		parent::__construct(new WorkflowRegistry($this));
	}
}
