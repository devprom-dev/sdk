<?php

class IssueStateDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			" ( SELECT GROUP_CONCAT(CAST(a.TaskType AS CHAR)) ".
 			"	  FROM pm_TaskTypeState a ".
            "    WHERE a.VPD = ".$alias.".VPD AND a.State = ".$alias.".ReferenceName ) TaskTypes ";
 		
 		return $columns;
 	}
}