<?php

class CapacityPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $project_it = getSession()->getProjectIt();
 	    
 		$columns = array();
 		
  		$predicate = $project_it->getDaysInWeek() < 6
  		    ? " AND i.StartDateWeekday NOT IN (1,7) " : ($project_it->getDaysInWeek() < 7 ? " AND i.StartDateWeekday <> 1 " : "");
  		
        $columns[] =
            " (SELECT COUNT(1) FROM pm_CalendarInterval i ".
            "   WHERE i.StartDateOnly BETWEEN t.StartDateOnly AND t.FinishDateOnly AND i.Kind = 'day' ".$predicate." ) PlannedCapacity ";

        $columns[] =
            " (SELECT COUNT(1) - 1 FROM pm_CalendarInterval i ".
            "   WHERE i.StartDateOnly BETWEEN t.StartDateOnly AND t.AdjustedFinish AND i.Kind = 'day' ".$predicate." ) ActualDurationInWorkingDays ";
        
        $columns[] =
            " (SELECT COUNT(1) FROM pm_CalendarInterval i ".
            "   WHERE i.StartDateOnly BETWEEN t.AdjustedStart AND t.FinishDateOnly AND i.Kind = 'day' ".$predicate." ) LeftCapacityInWorkingDays ";

        $columns[] =
            " (SELECT TO_DAYS(t.FinishDate) - TO_DAYS(t.StartDate) + LEAST(SIGN(TO_DAYS(NOW()) - TO_DAYS(t.StartDate)) + 1, 1)) Capacity ";
  
 		return $columns;
 	}
}
