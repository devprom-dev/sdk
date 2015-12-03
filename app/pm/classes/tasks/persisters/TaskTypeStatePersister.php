<?php

class TaskTypeStatePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT GROUP_CONCAT(st.Caption) FROM pm_TaskTypeState ts, pm_State st " .
			"  WHERE ts.TaskType = ".$this->getPK($alias).
			"    AND ts.State = st.ReferenceName ".
			"    AND ts.VPD = st.VPD ".
			"    AND st.ObjectClass = 'request' ) States ";

 		return $columns;
 	}
}
