<?php

class ReleaseUserHasTasksPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( $filter == '' )
 		{
		    return " AND EXISTS (SELECT 1 FROM pm_Task s, pm_ChangeRequest r ".
		    	   "			  WHERE s.Assignee IS NULL ".
			       " 				AND s.ChangeRequest = r.pm_ChangeRequestId ".
		    	   "			    AND r.PlannedRelease = t.pm_VersionId) ";
 		}
 		
 		if ( !is_numeric($filter) ) return " AND 1 = 2 ";
 		
	    return " AND EXISTS (SELECT 1 FROM pm_Task s, pm_ChangeRequest r ".
	    	   "			  WHERE s.Assignee = ".$filter.
	    	   "			    AND s.ChangeRequest = r.pm_ChangeRequestId ".
		       "			    AND r.PlannedRelease = t.pm_VersionId) ";
 	}
}
