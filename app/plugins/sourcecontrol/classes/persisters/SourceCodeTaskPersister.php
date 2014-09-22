<?php

 class SourceCodeTaskPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$trace = $model_factory->getObject('TaskTraceSourceCode');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.Task AS CHAR)) " .
			"     FROM pm_TaskTrace l " .
			"    WHERE l.ObjectId = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) Tasks " 
 		);
 	}
 }
