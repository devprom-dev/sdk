<?php

include "ReportWorkflowAnalysisList.php";
include "model/WorkflowTimeScale.php";

include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/StateExFilterWebMethod.php";

class ReportWorkflowAnalysisTable extends PMPageTable
{
 	function getFilterPredicates()
 	{
 		$values = $this->getFilterValues();
 		
 		return array_merge(
 				parent::getFilterPredicates(),
 				array (
		 			new StatePredicate( $values['state'] ),
					new FilterSubmittedAfterPredicate($values['submittedon']),
					new FilterSubmittedBeforePredicate($values['submittedbefore'])
		 		)
 			);
 	}

	function getList()
	{
		return new ReportWorkflowAnalysisList( $this->getObject() );
	}

	function getFilters()
	{
		$filters = array(
			new StateExFilterWebMethod(getFactory()->getObject('IssueState')->getAll()),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			$this->buildTimeScaleFilter()
		);
		
		return array_merge( $filters, parent::getFilters() ); 		
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
	
	function getNewActions()
	{
		return array();
	}
	
	function getActions()
	{
		return array();
	}
	
	function getDeleteActions()
	{
		return array();
	}
} 