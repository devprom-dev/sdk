<?php

include "WorkTableStateRegistry.php";

class WorkTableState extends Metaobject
{
	public function __construct()
	{
		parent::__construct('pm_State', new WorkTableStateRegistry()); 
	}
}