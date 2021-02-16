<?php
include_once SERVER_ROOT_PATH."pm/views/issues/RequestTable.php";
include "ReportWorkflowAnalysisList.php";
include "model/WorkflowTimeScale.php";

class ReportWorkflowAnalysisTable extends RequestTable
{
	function getList() {
		return new ReportWorkflowAnalysisList( $this->getObject() );
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

	function getDetailsParms() {
        return array();
    }
} 