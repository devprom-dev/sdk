<?php

class RequestFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			"(SELECT ROUND(SUM(ac.Capacity), 1) FROM pm_Activity ac, pm_Task ts ".
 			"  WHERE ts.ChangeRequest = ".$this->getPK($alias).
 			"    AND ts.pm_TaskId = ac.Task ) Fact " );

 		return $columns;
 	}
}
