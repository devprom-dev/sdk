<?php

class TaskSpentTimePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			"(SELECT GROUP_CONCAT(CAST(ac.pm_ActivityId AS CHAR)) FROM pm_Activity ac ".
 			"  WHERE ac.Task = ".$this->getPK($alias).") Spent " );

 		return $columns;
 	}
}
