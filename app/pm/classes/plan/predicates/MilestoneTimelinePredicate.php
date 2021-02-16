<?php

class MilestoneTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
			    return " AND '".SystemDateTime::date()."' > IFNULL(t.MilestoneDate, NOW()) " .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_ChangeRequestTrace tr, pm_ChangeRequest r " .
			   		   "			       WHERE r.pm_ChangeRequestId = tr.ChangeRequest " .
                       "                     AND tr.ObjectId = t.pm_MilestoneId ".
                       "                     AND tr.ObjectClass = 'Milestone' ".
			   		   "				     AND r.FinishDate IS NULL )";
			case 'not-passed':
				return " AND ('".SystemDateTime::date()."' <= IFNULL(t.MilestoneDate, NOW()) " .
                    "           OR EXISTS ( SELECT 1 FROM pm_ChangeRequestTrace tr, pm_ChangeRequest r " .
                    "			       WHERE r.pm_ChangeRequestId = tr.ChangeRequest " .
                    "                     AND tr.ObjectId = t.pm_MilestoneId ".
                    "                     AND tr.ObjectClass = 'Milestone' ".
                    "				     AND r.FinishDate IS NULL )) ";
            case 'overdue':
                return " AND t.MilestoneDate < '".SystemDateTime::date()."' ".
                        " AND EXISTS ( SELECT 1 FROM pm_ChangeRequestTrace tr, pm_ChangeRequest r " .
                        "			       WHERE r.pm_ChangeRequestId = tr.ChangeRequest " .
                        "                     AND tr.ObjectId = t.pm_MilestoneId ".
                        "                     AND tr.ObjectClass = 'Milestone' ".
                        "				     AND r.FinishDate IS NULL )";

		    default:
				return " AND 1 = 2 ";
 		}
 	}
}
