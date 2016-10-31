<?php
include "ProjectAccessibleRegistry.php";

class ProjectAccessible extends Project
{
	function __construct() {
		parent::__construct(new ProjectAccessibleRegistry());
	}
}