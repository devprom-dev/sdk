<?php

class TaskPlanFactPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('PlanFact');
	}

	function getSelectColumns( $alias )
 	{
 		return array(
            " (SELECT IFNULL(t.Planned, 0) - IFNULL(SUM(a.Capacity), 0) FROM pm_Activity a WHERE a.Task = t.pm_TaskId) PlanFact ",
            " CONCAT(UNIX_TIMESTAMP(t.FinishDate), ':', t.pm_TaskId) PlanFactTick "
        );
 	}
}
