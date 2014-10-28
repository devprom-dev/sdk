<?php

class StageTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
 			    
 			    return " AND '".SystemDateTime::date()."' > GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW())) " .
			   		   " AND t.UncompletedItems < 1 ";
					   
			case 'current':
			    
 			    return " AND '".SystemDateTime::date()."' ".
					   "	 BETWEEN GREATEST(t.EstimatedStartDate, t.StartDate) ".
					   "	     AND GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), DATE(IFNULL(t.FinishDate, NOW()))) " .
			   		   " OR t.UncompletedItems > 0 ";
			    
			case 'not-passed':
			    
 			    return " AND '".SystemDateTime::date()."' <= GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW())) " .
			   		   " OR t.UncompletedItems > 0 ";
 			    
 			default:
 			    
				return " AND 1 = 2 ";
 		}
 	}
}
