<?php

class TaskTracePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$trace = getFactory()->getObject('TaskTraceTask');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_TaskTrace l " .
			"    WHERE l.Task = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceTask ",
 				
 			" ( SELECT GROUP_CONCAT(CAST(l.Task AS CHAR)) " .
			"     FROM pm_TaskTrace l " .
			"    WHERE l.ObjectId = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceInversedTask ",
 			
 			" ( SELECT GROUP_CONCAT(CONCAT_WS(':',CAST(l.ObjectId AS CHAR), tr.State)) " .
			"     FROM pm_TaskTrace l, " .
 			"		   pm_Task tr ".
			"    WHERE l.Task = " .$this->getPK($alias).
 			"	   AND l.ObjectId = tr.pm_TaskId ".
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceTaskInfo "
 		);
 	}

 	function IsPersisterImportant() {
        return true;
    }
}

