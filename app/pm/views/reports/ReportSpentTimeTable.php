<?php
include "ActivitiesExcelIterator.php";

class ReportSpentTimeTable extends PMPageTable
{
    private $rowsObject = null;

	function getList($mode = '') {
		return new ReportSpentTimeList( $this->getObject() );
	}

	function getFilters()
	{
	    $filters = array_merge(
            parent::getFilters(),
            array(
                $this->buildStartFilter(),
                new ViewFinishDateWebMethod(),
                $this->buildUserFilter(),
                $this->buildFilterState()
            )
        );
        $filters[] = $this->buildUserRoleFilter();

        return $filters;
	}

    function getFilterPredicates( $values )
	{
 		return array_merge(
 		    parent::getFilterPredicates( $values ),
            array(
                new FilterSubmittedAfterPredicate($values['start']),
                new FilterSubmittedBeforePredicate($values['finish']),
                new SpentTimeStatePredicate($values['state']),
                new FilterAttributePredicate( 'SystemUser', $this->getFilterUsers($values['participant'],$values) )
            )
        );
	}
	
	function getSortFields()
	{
		return array();
	}

    function getRowsObject()
    {
        if ( is_object($this->rowsObject) ) return $this->rowsObject;

        $values = $this->getFilterValues();
        if ( $values['rowsobject'] != '' ) {
            return $this->rowsObject = getFactory()->getObject($values['rowsobject']);
        }

        switch( $this->getReportBase() )
        {
            case 'activitiesreporttasks':
                return $this->rowsObject = getFactory()->getObject('Task');
            case 'activitiesreportincrements':
                return $this->rowsObject = getFactory()->getObject('Increment');
            case 'activitiesreportusers':
                return $this->rowsObject = getFactory()->getObject('User');
            case 'activitiesreportproject':
                return $this->rowsObject = getFactory()->getObject('Project');
            default:
                return $this->rowsObject = getFactory()->getObject('Request');
        }
    }

	function getActions()
	{
		$actions = array();
		$values = $this->getFilterValues();
        $values['rowsobject'] = get_class($this->getRowsObject());

		$method = new ExcelExportWebMethod();
		$actions[] = array(
			'name' => text(2203),
			'url' => $method->url(translate('Затраченное время'), 'ActivitiesExcelIterator', $values)
		);

        unset($values['month']);
        unset($values['year']);
		$method = new ExcelExportWebMethod();
		$actions[] = array (
			'url' => $method->url('', 'ActivityPivotIteratorExportExcel', $values),
			'name' => text('ee208')
		);

		return $actions;
	}

	function getNewActions() {
		return array();
	}

	function getExportActions() {
		return array();
	}

	function getDeleteActions() {
		return array();
	}

	function buildStartFilter() {
        $filter = new ViewStartDateWebMethod();
        return $filter;
    }

    function buildFilterValuesByDefault(&$filters)
    {
        $values = parent::buildFilterValuesByDefault($filters);
        if ( !array_key_exists('start', $values) ) {
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime(date('Y-m-1')));
        }
        return $values;
    }

    function buildUserFilter() {
	    return new FilterObjectMethod(getFactory()->getObject('User'), '', 'participant');
    }

    function buildFilterState($filterValues = array())
    {
        $filter = new FilterObjectMethod( getFactory()->getObject('StateCommon'), translate('Состояние'), 'state' );
        $filter->setHasNone(false);
        return $filter;
    }

    function buildSearchPredicate($values) {
    }

    protected function getFamilyModules( $module )
    {
        return array (
            'worklog',
            'issuesestimation',
            'requirementseffort',
            'tasksefforts'
        );
    }

    protected function getChartModules( $module )
    {
        return array (
            'tasksplanbyfact',
            'activitieschart'
        );
    }

    function getDetails() {
        return array();
    }

    function buildAttributesPredicates($values) {
        return array();
    }
}