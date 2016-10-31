<?php

class StageTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
 			    return " AND '".SystemDateTime::date('Y-m-d')."' > DATE(GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW()))) " .
			   		   " AND t.UncompletedIssues < 1 AND t.UncompletedTasks < 1 ";
					   
			case 'current':
 			    return " AND '".SystemDateTime::date('Y-m-d')."' ".
					   "	 BETWEEN DATE(GREATEST(t.EstimatedStartDate, t.StartDate)) ".
					   "	     AND DATE(GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), DATE(IFNULL(t.FinishDate, NOW())))) " .
			   		   " OR (t.UncompletedIssues + t.UncompletedTasks) > 0 ";
			    
			case 'not-passed':
 			    return " AND '".SystemDateTime::date('Y-m-d')."' <= DATE(GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW()))) " .
			   		   " OR (t.UncompletedIssues + t.UncompletedTasks) > 0 ";
 			    
 			default:
				return " AND 1 = 2 ";
 		}
 	}
}
