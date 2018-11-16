<?php
include "WorkItemChartWidget.php";

class WorkItemChart extends PMPageChart
{
    function buildIterator()
	{
	    $startFilter = new SortAttributeClause('StartDate');
        $startFilter->setNullOnTop(false);

        $filters = $this->getTable()->getFilterPredicates();
        foreach( $filters as $key => $filter ) {
            if ( $filter instanceof FilterBaseVpdPredicate ) {
                unset($filters[$key]);
            }
        }

		return $this->getObject()->getRegistry()->Query(
            array_merge(
                $filters,
                array (
                    $startFilter,
                    new SortAttributeClause('PlannedStartDate'),
                    new SortAttributeClause('FinishDate'),
                    new FilterAttributePredicate('Assignee', getFactory()->getObject('ProjectUser')->getAll()->idsToArray())
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
                    ? strftime('%Y-%m-%d', strtotime('+14 day', strtotime(SystemDateTime::date('Y-m-d'))))
                    : getLanguage()->getDbDate($values['finish']);

		return new WorkItemChartWidget($this->getIterator(), $start, $finish);
	}
	
	function getStyle()
	{
	}
	
	function drawLegend( $data, & $aggs )
	{
	}
}