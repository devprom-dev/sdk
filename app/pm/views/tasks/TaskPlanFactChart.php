<?php

include "TaskPlanFactChartWidget.php";

class TaskPlanFactChart extends PMPageChart
{
	function getChartWidget()
	{
		$widget = new TaskPlanFactChartWidget();
        $widget->setIterator($this->getIterator());
		return $widget;
	}
	
	function getColumnFields()
	{
		return array();
	}
	
	function getGroupFields()
	{
		return array();
	}
	
	function getAggByFields()
	{
		return array();
	}
	
	function getAggregators()
	{
		return array();
	}

	function getOptions($filter_values)
    {
        return array();
    }

    function getGroup() {
	    return 'PlanFactTick';
    }

    function getAggregateBy() {
	    return 'PlanFact';
    }

    function getAggregator() {
	    return 'SUM';
    }

    function getLegendVisible() {
        return false;
    }

    function getTableVisible() {
	    return false;
    }
}