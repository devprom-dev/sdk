<?php

class TaskTypeBaseIterationRelatedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS ( SELECT 1 FROM pm_Task ts, pm_TaskType tt " .
			   "			   WHERE ts.TaskType = tt.pm_TaskTypeId" .
 			   "				 AND tt.ParentTaskType = t.pm_TaskTypeId ".
			   "			     AND ts.Release = ".$filter.") ";
 	}
}
