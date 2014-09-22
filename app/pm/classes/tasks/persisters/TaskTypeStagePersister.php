<?php

class TaskTypeStagePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT GROUP_CONCAT(CAST(ts.ProjectStage AS CHAR)) FROM pm_TaskTypeStage ts " .
			"  WHERE ts.TaskType = ".$this->getPK($alias)." ) Stages ";

 		return $columns;
 	}
}
