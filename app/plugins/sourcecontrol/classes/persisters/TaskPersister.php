<?php

 class TaskSourceCodePersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$trace = $model_factory->getObject('TaskTraceSourceCode');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_TaskTrace l " .
			"    WHERE l.Task = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) SourceCode " 
 		);
 	}
 }
