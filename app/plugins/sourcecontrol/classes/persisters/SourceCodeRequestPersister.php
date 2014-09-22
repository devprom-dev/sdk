<?php

 class SourceCodeRequestPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$trace = $model_factory->getObject('RequestTraceSourceCode');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(l.ChangeRequest AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ObjectId = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) Issues "  
 		);
 	}
 }
