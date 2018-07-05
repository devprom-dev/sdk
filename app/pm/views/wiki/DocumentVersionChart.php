<?php
include SERVER_ROOT_PATH . "pm/classes/plan/PlanChartData.php";
include "DocumentVersionChartWidget.php";

class DocumentVersionChart extends PMPageChart
{
    function getIterator() {
        return PageList::getIterator();
	}
	
	function getAggregators() 
	{
		return array();
	}
	 		
	function getAggByFields()
	{
		return array();
	}
	
	function getGroupFields()
	{
		return array();
	}
	
	function getChartWidget()
	{
		return new DocumentVersionChartWidget($this->getIterator());
	}
	
	function getStyle()
	{
	}
	
	function drawLegend( $data, & $aggs )
	{
	}
}