<?php
include "ProjectLinkedActiveRegistry.php";

class ProjectLinkedActive extends Project
{
	function __construct() {
		parent::__construct(new ProjectLinkedActiveRegistry($this));
	}
}