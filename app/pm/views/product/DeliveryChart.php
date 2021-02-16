<?php
include "DeliveryChartWidget.php";

class DeliveryChart extends PMPageChart
{
	function buildIterator()
	{
	    $registry = $this->getObject()->getRegistry();
        $registry->setLimit(1024);
		return $registry->Query(
				array_merge(
					$this->getTable()->getFilterPredicates($this->getTable()->getPredicateFilterValues()),
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
		return new DeliveryChartWidget($this->getIterator());
	}

    function getOptions($filter_values)
    {
        return array();
    }

	function getStyle()
	{
	}
	
	function drawLegend( $data, & $aggs )
	{
	}
}