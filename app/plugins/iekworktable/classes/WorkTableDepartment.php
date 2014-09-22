<?php

include "WorkTableDepartmentRegistry.php";

class WorkTableDepartment extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new WorkTableDepartmentRegistry()); 
	}
}