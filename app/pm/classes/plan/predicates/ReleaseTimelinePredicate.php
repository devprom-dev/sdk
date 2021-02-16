<?php

class ReleaseTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
			    return " AND '".SystemDateTime::date()."' > IFNULL(t.FinishDate, NOW()) " .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.FinishDate IS NULL )";
			case 'current':
				return " AND t.IsClosed = 'N' AND ('".SystemDateTime::date()."' BETWEEN t.StartDate AND IFNULL(t.FinishDate, NOW()) " .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.FinishDate IS NULL ))";
			case 'not-passed':
				return " AND t.IsClosed = 'N' AND ('".SystemDateTime::date()."' <= IFNULL(t.FinishDate, NOW()) " .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.FinishDate IS NULL ))";
            case 'overdue':
                return " AND t.FinishDate < DATE(NOW()) ";

		    default:
				return " AND 1 = 2 ";
 		}
 	}
}
