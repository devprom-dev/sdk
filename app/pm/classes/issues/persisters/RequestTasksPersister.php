<?php

class RequestTasksPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =
 	 		"(SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) Tasks ";

 		$states = getFactory()->getObject('Task')->getTerminalStates();
 		if ( count($states) < 1 ) $states[] = 'dummy';
 		
 		$columns[] =
 	 		"(SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias).
 			"	 AND s.State NOT IN ('".join("','",$states)."') ) OpenTasks ";
 		
 		return $columns;
 	}
}
