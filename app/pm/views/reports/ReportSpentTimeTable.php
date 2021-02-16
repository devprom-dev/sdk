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
		return array_merge(
		    parent::getFilters(),
            array(
                $this->buildStartFilter(),
                new ViewFinishDateWebMethod(),
                $this->buildUserFilter(),
                $this->buildFilterState()
            )
		);
	}

    function getFilterPredicates( $values )
	{
 		$predicates = array(
 		    new FilterSubmittedAfterPredicate($values['start']),
            new FilterSubmittedBeforePredicate($values['finish']),
            new SpentTimeStatePredicate($values['state'])
        );
 		if ( !in_array($values['participant'], array('','all','none')) ) {
			$predicates[] = new FilterAttributePredicate('SystemUser', $values['participant']);
 		}

 		return array_merge(
 		    parent::getFilterPredicates( $values ),
            $predicates
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
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j'))));
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

    protected function getFamilyModules( $module )
    {
        return array (
            'worklog'
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
}