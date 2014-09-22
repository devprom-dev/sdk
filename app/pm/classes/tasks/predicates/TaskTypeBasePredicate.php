<?php

class TaskTypeBasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM pm_TaskType tt ".
			   "			  WHERE tt.pm_TaskTypeId = t.TaskType AND tt.ParentTaskType = ".$filter.")";
 	}
}
