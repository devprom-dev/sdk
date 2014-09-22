<?php

include "FlotChartVelocityWidget.php";

class VelocityChart extends PMPageChart
{
 	function __construct( $object )
 	{
		parent::__construct( $object );
 	}

	function _getGroupFields() 
	{
		return array();
	}
	 		
	function _getAggByFields()
	{
		return array();
	}
	
	function getChartWidget()
	{
		$flot = new FlotChartVelocityWidget();
		
		//$flot->showLegend( false );
		//$flot->showPoints( false );
		
		return $flot;
	}
}