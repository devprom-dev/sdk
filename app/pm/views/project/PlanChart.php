<?php
include SERVER_ROOT_PATH . "pm/classes/plan/PlanChartData.php";
include "PlanChartWidget.php";

class PlanChart extends PMPageChart
{
    function __construct() {
        parent::__construct( new PlanChartData() );
    }

    function buildIterator()
	{
		return $this->getObject()->getRegistry()->Query(
            array_merge(
                $this->getTable()->getFilterPredicates(),
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

	function getOptions($filter_values)
    {
        return array();
    }

    function getChartWidget()
	{
	    $values = $this->getFilterValues();

        $start = in_array($values['start'], array('','all','hide'))
                    ? strftime('%Y-%m-%d', strtotime('-7 day', strtotime(SystemDateTime::date('Y-m-d'))))
                    : getLanguage()->getDbDate($values['start']);
        $finish = in_array($values['finish'], array('','all','hide'))
                    ? strftime('%Y-%m-%d', strtotime('+1 month', strtotime(SystemDateTime::date('Y-m-d'))))
                    : getLanguage()->getDbDate($values['finish']);

		return new PlanChartWidget($this->getIterator(), $start, $finish);
	}
	
	function getStyle()
	{
	}
	
	function drawLegend( $data, & $aggs )
	{
	}
}