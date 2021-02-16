<?php

class StageTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
 			    return " AND '".SystemDateTime::date('Y-m-d')."' > IFNULL(t.FinishDate, NOW()) " .
			   		   " AND t.UncompletedIssues < 1 AND t.UncompletedTasks < 1 ";
					   
			case 'not-passed':
 			    return " AND ('".SystemDateTime::date('Y-m-d')."' <= IFNULL(t.FinishDate, NOW()) " .
			   		   "        OR (t.UncompletedIssues + t.UncompletedTasks) > 0) ";

            case 'overdue':
                return " AND t.EstimatedFinishDate > t.FinishDate ";

 			default:
				return " AND 1 = 2 ";
 		}
 	}
}
