<?php
include "ProjectActiveRegistry.php";

class ProjectActive extends Project
{
 	function __construct() {
		parent::__construct(new ProjectActiveRegistry($this));
 	}
}
