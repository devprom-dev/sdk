<?php

include "ActivitiesExcelIterator.php";
include_once SERVER_ROOT_PATH."pm/methods/ViewSpentTimeUserWebMethod.php";

class ReportSpentTimeTable extends PMPageTable
{
	function getList()
	{
		return new ReportSpentTimeList( $this->getObject() );
	}

	function getFilters()
	{
		return array_merge( parent::getFilters(), 
				array(
					new ViewSpentTimeWebMethod(),
					new ViewDateYearWebMethod(),
					new ViewDateMonthWebMethod(),
					new ViewSpentTimeUserWebMethod()			
				)
		);
	}

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
 		$this->getObject()->setView( $values['view'] );
 		$this->getObject()->setReportYear( $values['year'] );
 		$this->getObject()->setReportMonth( $values['month'] ); 
 		
 		$predicates = array();
 		if ( !in_array($values['participant'], array('','all','none')) ) {
			$predicates[] = new FilterAttributePredicate('SystemUser', preg_split('/,/', $values['participant']));
 		}
 		
		$predicates[] = new ActivityReportYearPredicate($values['year']);
		$predicates[] = new ActivityReportMonthPredicate($values['month']);
 		
 		return $predicates;// array_merge($predicates, parent::getFilterPredicates());
	}
	
	function getSortFields()
	{
		return array();
	}
	
	function getActions()
	{
		$actions = array();

		$method = new ExcelExportWebMethod();

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( translate('Затраченное время'), 'ActivitiesExcelIterator') ) );

		return $actions;
	}

	function getNewActions()
	{
		return array();
	}
	
	function getDeleteActions()
	{
		return array();
	}
} 