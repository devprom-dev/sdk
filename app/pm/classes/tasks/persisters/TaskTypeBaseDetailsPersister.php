<?php

class TaskTypeBaseDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT COUNT(1) FROM pm_TaskType ts ".
 			"  WHERE ts.ParentTaskType = ".$this->getPK($alias).
 			"    AND ts.VPD = '".getSession()->getProjectIt()->get('VPD')."' ) SubTypesCount ";

 		return $columns;
 	}
}
