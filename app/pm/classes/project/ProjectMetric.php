<?php
include "ProjectMetricRegistry.php";

class ProjectMetric extends Metaobject
{
	function __construct() {
		parent::__construct('pm_ProjectMetric', new ProjectMetricRegistry($this));
	}
}