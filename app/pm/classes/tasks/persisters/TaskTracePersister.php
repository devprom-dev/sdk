<?php

class TaskTracePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$trace = $model_factory->getObject('TaskTraceTask');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_TaskTrace l " .
			"    WHERE l.Task = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceTask " 
 		);
 	}
}

