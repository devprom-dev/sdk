<?php

include "ActivitiesExcelIterator.php";
include_once SERVER_ROOT_PATH."pm/methods/ViewSpentTimeUserWebMethod.php";

class ReportSpentTimeTable extends PMPageTable
{
	function getList() {
		return new ReportSpentTimeList( $this->getObject() );
	}

	function getFilters()
	{
		return array_merge(
		    parent::getFilters(),
            array(
                new ViewSpentTimeWebMethod(),
                $this->buildStartFilter(),
                new ViewFinishDateWebMethod(),
                new ViewSpentTimeUserWebMethod()
            )
		);
	}

	function getFiltersDefault() {
        return array('view', 'start', 'finish');
    }

    function getFilterPredicates()
	{
		$values = $this->getFilterValues();
        $this->getObject()->setView( $values['view'] );
		
 		$predicates = array(
 		    new FilterSubmittedAfterPredicate($values['start']),
            new FilterSubmittedBeforePredicate($values['finish'])
        );
 		if ( !in_array($values['participant'], array('','all','none')) ) {
			$predicates[] = new FilterAttributePredicate('SystemUser', preg_split('/,/', $values['participant']));
 		}

 		return array_merge(
 		    parent::getFilterPredicates(),
            $predicates
        );
	}
	
	function getSortFields()
	{
		return array();
	}
	
	function getActions()
	{
		$actions = array();
		$values = $this->getFilterValues();

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
        $filter->setDefault(getSession()->getLanguage()->getPhpDate(strtotime('-3 weeks', strtotime(date('Y-m-j')))));
        return $filter;
    }

    protected function getFamilyModules( $module )
    {
        switch( $module ) {
            case 'project-spenttime':
                return array (
                    'tasksplanbyfact',
                    'worklog'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }
}