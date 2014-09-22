<?php

class StageTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$now = SystemDateTime::convertToServerTime(SystemDateTime::date('Y-m-d'));
 		
 		switch ( $filter )
 		{
 			case 'past':
 			    
 			    return " AND '".$now."' > GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW())) " .
			   		   " AND t.UncompletedItems < 1 ";
					   
			case 'current':
			    
 			    return " AND '".$now."' ".
					   "	 BETWEEN GREATEST(t.EstimatedStartDate, t.StartDate) ".
					   "	     AND GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), DATE(IFNULL(t.FinishDate, NOW()))) " .
			   		   " OR t.UncompletedItems > 0 ";
			    
			case 'not-passed':
			    
 			    return " AND '".$now."' <= GREATEST(IFNULL(t.EstimatedFinishDate, NOW()), IFNULL(t.FinishDate, NOW())) " .
			   		   " OR t.UncompletedItems > 0 ";
 			    
 			default:
 			    
				return " AND 1 = 2 ";
 		}
 	}
}
