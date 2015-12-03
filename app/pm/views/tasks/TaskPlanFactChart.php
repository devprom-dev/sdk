<?php

include_once SERVER_ROOT_PATH."pm/classes/tasks/persisters/TaskPlanFactPersister.php";
include "TaskPlanFactChartWidget.php";

class TaskPlanFactChart extends PMPageChart
{
 	function __construct( $object )
 	{
		$object->addPersister( new TaskPlanFactPersister() );
		parent::__construct( $object );
 	}

	function getChartWidget()
	{
		$widget = new TaskPlanFactChartWidget();
		$widget->setIterator( $this->getObject()->getAll() );
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
}