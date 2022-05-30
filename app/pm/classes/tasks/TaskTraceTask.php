<?php
include_once "TaskTraceBase.php";
include "validators/ModelValidatorTaskTraces.php";

class TaskTraceTask extends TaskTraceBase
{
 	function getObjectClass() {
 		return 'Task';
 	}

    function duplicateInRelatedRequest() {
        return false;
    }

    function getValidators() {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelValidatorTaskTraces()
            )
        );
    }
}