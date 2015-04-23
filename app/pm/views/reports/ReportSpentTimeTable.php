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
 		
 		if ( !in_array($values['participant'], array('','all','none')) ) {
 			$this->getObject()->setParticipantFilter( preg_split('/,/', $values['participant']));
 		}
 		
 		return parent::getFilterPredicates();
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