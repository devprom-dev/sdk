<?php
include "DocumentVersionChartWidget.php";

class DocumentVersionChart extends PMPageChart
{
    function buildIterator() {
        return PageList::buildIterator();
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