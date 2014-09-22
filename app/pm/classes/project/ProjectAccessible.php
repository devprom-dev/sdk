<?php

include "ProjectAccessibleRegistry.php";
include "predicates/ProjectAccessiblePredicate.php";

class ProjectAccessible extends Metaobject
{
	function __construct()
	{
		parent::__construct('pm_Project', new ProjectAccessibleRegistry());
	}
}