<?php

class TaskReleasePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT r.PlannedRelease FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) PlannedRelease ";
 		
 		return $columns;
 	}
}

