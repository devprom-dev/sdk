<?php

include "ProjectExceptCurrentRegistry.php";

class ProjectExceptCurrent extends Metaobject
{
	function __construct()
	{
		parent::__construct('pm_Project', new ProjectExceptCurrentRegistry());
	}
}