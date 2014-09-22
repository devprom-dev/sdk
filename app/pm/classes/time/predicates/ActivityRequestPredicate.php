<?php

class ActivityRequestPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM pm_Task s ".
			   "			  WHERE s.ChangeRequest IN (".$filter.") ".
			   "			    AND s.pm_TaskId = t.Task) ";
 	}
}
