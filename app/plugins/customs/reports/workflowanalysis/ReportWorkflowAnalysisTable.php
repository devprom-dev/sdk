<?php
include "ReportWorkflowAnalysisList.php";
include "model/WorkflowTimeScale.php";

include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/StateExFilterWebMethod.php";
include_once SERVER_ROOT_PATH."pm/views/issues/RequestTable.php";

class ReportWorkflowAnalysisTable extends RequestTable
{
 	function getFilterPredicates()
 	{
 		$values = $this->getFilterValues();
 		return array_merge(
 				parent::getFilterPredicates(),
 				array (
		 		)
 			);
 	}

	function getList() {
		return new ReportWorkflowAnalysisList( $this->getObject() );
	}

	function getFilters()
	{
		$filters = array(
			$this->buildTimeScaleFilter()
		);
		return array_merge( parent::getFilters(), $filters );
	}
	
	function buildTimeScaleFilter()
	{
		$fitler = new FilterObjectMethod( new WorkflowTimeScale(), '', 'timescale' );
		
		$fitler->setDefaultValue(1);
		$fitler->setIdFieldName('ReferenceName');
		$fitler->setType('singlevalue');
		$fitler->setHasNone( false );
		$fitler->setHasAll( false );
		
		return $fitler;
	}
	
	function getNewActions() {
		return array();
	}
	
	function getActions() {
		return array();
	}
	
	function getDeleteActions() {
		return array();
	}

	function getBulkActions() {
		return array();
	}
} 