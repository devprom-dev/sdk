<?php

class RequestSpentTimePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			"(SELECT GROUP_CONCAT(CAST(ac.pm_ActivityId AS CHAR)) FROM pm_Activity ac, pm_Task ts ".
 			"  WHERE ts.ChangeRequest = ".$this->getPK($alias).
 			"    AND ts.pm_TaskId = ac.Task ) Spent " );
 		
 		return $columns;
 	}
}
