<?php
include SERVER_ROOT_PATH . "pm/classes/plan/PlanChartData.php";
include "PlanChartWidget.php";

class PlanChart extends PMPageChart
{
    function __construct() {
        parent::__construct( new PlanChartData() );
    }

    function getIterator()
	{
		return $this->getObject()->getRegistry()->Query(
				array_merge(
					array (
                        new SortAttributeClause('Project'),
                        new SortAttributeClause('FinishDate'),
                        new SortAttributeClause('SortIndex')
					)
				)
		);
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
		return new PlanChartWidget($this->getIterator());
	}
	
	function getStyle()
	{
	}
	
	function drawLegend( $data, & $aggs )
	{
	}
}