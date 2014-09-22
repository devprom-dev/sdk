<?php

class ActivityOtherIterationsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.Iteration <> ".$filter.
			   " AND EXISTS (SELECT 1 FROM pm_Task s ".
			   "			  WHERE s.Release = ".$filter.
			   "				AND s.pm_TaskId = t.Task) ";
 	}
}
