<?php

include "TaskPlanFactChartWidget.php";

class TaskPlanFactChart extends PMPageChart
{
	function getChartWidget()
	{
		$widget = new TaskPlanFactChartWidget();
		$widget->setIterator( PageList::getIterator() );
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
	    return 'FinishDate';
    }

    function getAggregateBy() {
	    return 'PlanFact';
    }

    function getGroupFunction() {
	    return 'AVG';
    }

    function getLegendVisible() {
        return false;
    }

    function getTableVisible() {
	    return false;
    }
}