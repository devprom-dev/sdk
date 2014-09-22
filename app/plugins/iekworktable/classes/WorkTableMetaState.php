<?php

include "WorkTableMetaStateRegistry.php";

class WorkTableMetaState extends Metaobject
{
	public function __construct()
	{
		parent::__construct('pm_State', new WorkTableMetaStateRegistry()); 
	}
}