<?php

 class RequirementCodePersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" ( SELECT GROUP_CONCAT(CAST(l.ObjectId AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l, pm_ChangeRequestTrace rl " .
			"    WHERE l.ChangeRequest = rl.ChangeRequest ".
 			"      AND l.ObjectClass = '".getFactory()->getObject('RequestTraceSourceCode')->getObjectClass()."' ".
			"      AND rl.ObjectId = " .$this->getPK($alias).
			"      AND rl.ObjectClass = '".getFactory()->getObject('RequestTraceRequirement')->getObjectClass()."' ".
			"      AND rl.Type = '".REQUEST_TRACE_PRODUCT."') SourceCode "
 		);
 	}
 }
