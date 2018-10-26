<?php
include "ProjectLinkedActiveRegistry.php";

class ProjectLinkedActive extends Metaobject
{
	function __construct() {
		parent::__construct('pm_Project', new ProjectLinkedActiveRegistry($this));
	}
}