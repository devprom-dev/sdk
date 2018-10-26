<?php

class MilestoneActualPredicate extends FilterPredicate
{
    function _predicate( $filter )
 	{
		return " AND ( 
		            t.MilestoneDate >= CURDATE()
		            AND NOT EXISTS (SELECT 1 FROM pm_ChangeRequestTrace tr
		                             WHERE tr.ObjectId = t.pm_MilestoneId
		                               AND tr.ObjectClass = 'Milestone')
		            OR EXISTS (SELECT 1 FROM pm_ChangeRequestTrace tr, pm_ChangeRequest r
		                        WHERE tr.ChangeRequest = r.pm_ChangeRequestId
		                          AND r.FinishDate IS NULL
		                          AND tr.ObjectId = t.pm_MilestoneId
		                          AND tr.ObjectClass = 'Milestone') ) ";
 	}
}
