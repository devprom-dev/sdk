<?php
include "ProjectMetricsTable.php";
include "ProjectMetricsSettingBuilder.php";

class ProjectMetricsPage extends PMPage
{
    function needDisplayForm()
 	{
 		return false;
 	}

	function getObject()
	{
        $object = getFactory()->getObject('ProjectMetric');
        $object->setAttributeType('Metric', 'REF_MetricId');
		return $object;
	}

	function getTable()
	{
	    getSession()->addBuilder( new ProjectMetricsSettingBuilder() );
		return new ProjectMetricsTable($this->getObject());
	}
}