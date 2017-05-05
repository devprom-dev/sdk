<?php
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include "ProjectMetricsChart.php";

class ProjectMetricsTable extends PMPageTable
{
	function getList()
	{
		return new ProjectMetricsChart( $this->getObject() );
	}

    function getFilters()
    {
        $filters[] = new ViewStartDateWebMethod(translate('Начало'));
        $filters[] = new ViewFinishDateWebMethod();
        return $filters;
    }

	function getFiltersDefault()
	{
		return array('any');
	}

    function getFilterPredicates()
    {
        $values = $this->getFilterValues();
        return array (
            new FilterModifiedAfterPredicate( $values['start'] ),
            new FilterModifiedBeforePredicate( $values['finish'] ),
            new FilterAttributePredicate('Metric', $values['metric'])
        );
    }

	function getNewActions()
	{
		return array();
	}

    function getExportActions()
    {
        return array();
    }
}