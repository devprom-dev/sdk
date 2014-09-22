<?php

 class RequestSourceCodePersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$trace = $model_factory->getObject('RequestTraceSourceCode');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ChangeRequest = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) SourceCode "  
 		);
 	}
 }
