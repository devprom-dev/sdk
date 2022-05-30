<?php
include_once "TaskTraceTask.php";

class TaskInversedTraceTask extends TaskTraceTask
{
    function __construct() {
        parent::__construct();
    }
    
 	function createIterator() {
 		return new TaskInversedTraceBaseIterator( $this );
 	}
}
