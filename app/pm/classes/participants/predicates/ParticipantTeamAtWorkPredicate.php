<?php

class ParticipantTeamAtWorkPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    global $model_factory;
 	    
 	    $task = $model_factory->getObject('pm_Task');
 	    
 	    $states = $task->getTerminalStates();
 	    
 	    list( $year, $month ) = preg_split('/-/', $filter);
 	    
		return " AND EXISTS (SELECT 1 FROM pm_Task s" .
			   "				WHERE s.Assignee = t.pm_ParticipantId " .
			   "    			  AND s.State IN ('".join("','", $states)."') ".
			   ($year > 0 ? " AND YEAR(s.RecordModified) = " .$year : "").
			   ($month > 0 ? " AND MONTH(s.RecordModified) = " .$month : "").
			   "			    UNION" .
			   "			   SELECT 1 FROM pm_Activity a" .
			   "				WHERE a.Participant = t.pm_ParticipantId " .
			   ($year > 0 ? " AND YEAR(a.ReportDate) = " .$year : "" ).
			   ($month > 0 ? " AND MONTH(a.ReportDate) = ".$month : "")." ) ";
 	}
}
