<?php

class TaskPlanFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT IFNULL(t.Planned, 0) - IFNULL(SUM(a.Capacity), 0) FROM pm_Activity a WHERE a.Task = t.pm_TaskId) PlanFact ";

 		return $columns;
 	}
}
