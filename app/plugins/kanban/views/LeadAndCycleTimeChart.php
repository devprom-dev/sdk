<?php

include "FlotChartLeadAndCycleTimeWidget.php";

class LeadAndCycleTimeChart extends PMPageChart
{
 	function __construct( $object )
 	{
		parent::__construct( $object );
 	}

	function getChartWidget()
	{
		$widget = new FlotChartLeadAndCycleTimeWidget();
		
		$widget->setIterator( $this->getObject()->getAll() );
		
		return $widget;
	}
}