<?php

class IterationUserHasTasksPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( $filter == '' )
 		{
		    return " AND EXISTS (SELECT 1 FROM pm_Task s ".
		    	   "			  WHERE s.Assignee IS NULL ".
		    	   "			    AND s.Release = t.pm_ReleaseId) ";
 		}
 		
 		if ( !is_numeric($filter) ) return " AND 1 = 2 ";
 		
	    return " AND EXISTS (SELECT 1 FROM pm_Task s, pm_Participant p ".
	    	   "			  WHERE s.Assignee = p.pm_ParticipantId ".
	    	   "				AND p.SystemUser = ".$filter.
	    	   "			    AND s.Release = t.pm_ReleaseId) ";
 	}
}
