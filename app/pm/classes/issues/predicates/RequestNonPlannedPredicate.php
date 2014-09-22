<?php

class RequestNonPlannedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$task = $model_factory->getObject('pm_Task');
 		
 		return " AND NOT EXISTS ( ".
 			   "     SELECT 1 FROM pm_Task e, pm_Release i " .
 			   "	  WHERE e.ChangeRequest = t.pm_ChangeRequestId " .
 			   "		AND e.Release = i.pm_ReleaseId ".
 			   "		AND i.Version = t.PlannedRelease ".
 			   "		AND e.State NOT IN ('".join($task->getTerminalStates(), "','")."') ) ";
 	}
}
