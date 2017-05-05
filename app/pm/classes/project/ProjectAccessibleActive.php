<?php
include "ProjectAccessibleActiveRegistry.php";

class ProjectAccessibleActive extends Project
{
	function __construct() {
		parent::__construct(new ProjectAccessibleActiveRegistry());
	}
}