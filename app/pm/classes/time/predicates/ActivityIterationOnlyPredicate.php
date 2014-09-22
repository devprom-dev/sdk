<?php

class ActivityIterationOnlyPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.Iteration = ".$filter.
			   " AND NOT EXISTS (SELECT 1 FROM pm_Task s ".
			   "			      WHERE s.Release = t.Iteration ".
			   "				    AND s.pm_TaskId = t.Task) ";
 	}
}
