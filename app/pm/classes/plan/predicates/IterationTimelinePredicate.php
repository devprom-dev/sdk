<?php

class IterationTimelinePredicate extends FilterPredicate
{
	const PAST = 'past';
	const CURRENT = 'current';
	const NOTPASSED = 'not-passed';
	
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case self::PAST:
				return " AND '".SystemDateTime::date('Y-m-d')."' > DATE(t.FinishDate) " .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_Task s, pm_Methodology m " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
                       "                     AND m.VPD = t.VPD ".
                       "                     AND m.IsTasks = 'Y' ".
			   		   "				     AND s.FinishDate IS NULL )".
                       " AND NOT EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.FinishDate IS NULL )";

 			case self::CURRENT:
				return " AND ('".SystemDateTime::date('Y-m-d')."' BETWEEN DATE(t.StartDate) AND DATE(t.FinishDate) " .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_Task s, pm_Methodology m " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
                       "                     AND m.VPD = t.VPD ".
                       "                     AND m.IsTasks = 'Y' ".
			   		   "				     AND s.FinishDate IS NULL)".
                       "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.FinishDate IS NULL) )";

 			case self::NOTPASSED:
				return " AND ('".SystemDateTime::date('Y-m-d')."' <= t.FinishDate " .
                       "      OR EXISTS ( SELECT 1 FROM pm_Task s, pm_Methodology m " .
                       "			       WHERE s.Release = t.pm_ReleaseId" .
                       "                     AND m.VPD = t.VPD ".
                       "                     AND m.IsTasks = 'Y' ".
                       "				     AND s.FinishDate IS NULL )".
                       "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.FinishDate IS NULL) )";
				
		    default:
				return " AND 1 = 2 ";
 		}
 	}
}
