<?php

include "WorkTableProjectRegistry.php";

class WorkTableProject extends Metaobject
{
	public function __construct()
	{
		parent::__construct('pm_Project', new WorkTableProjectRegistry());
	}
	
	static public function getProgramIt()
	{
		return getFactory()->getObject('Project')->getByRef('CodeName', 'DIT');
	}
}