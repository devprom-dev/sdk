<?php

class MilestoneRequestPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$trace = getFactory()->getObject('RequestTraceMilestone');
 		return array(
 			" ( SELECT GROUP_CONCAT(CAST(l.ChangeRequest AS CHAR)) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ObjectId = " .$this->getPK($alias).
 			"      AND l.ObjectClass = '".$trace->getObjectClass()."' ) TraceRequests " 
 		);
 	}
}
 