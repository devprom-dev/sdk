<?php
include "ProjectMetricsTable.php";

class ProjectMetricsPage extends PMPage
{
 	function needDisplayForm()
 	{
 		return false;
 	}

	function getObject()
	{
		return getFactory()->getObject('ProjectMetric');
	}

	function getTable()
	{
		return new ProjectMetricsTable($this->getObject());
	}
}