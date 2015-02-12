<?php

class TaskDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " IF( ".
 					 "		(SELECT IFNULL(t.FinishDate, i.FinishDate) FROM pm_Release i WHERE i.pm_ReleaseId = t.Release) IS NULL, 365, ".
 					 "		GREATEST(0, TO_DAYS((SELECT IFNULL(t.FinishDate, i.FinishDate) FROM pm_Release i WHERE i.pm_ReleaseId = t.Release)) - TO_DAYS(NOW())) ".
 					 " ) DueDays ";
 		$columns[] = " IF( ".
 					 " 		(SELECT IFNULL(t.FinishDate, i.FinishDate) FROM pm_Release i WHERE i.pm_ReleaseId = t.Release) IS NULL, 5, ".
 					 " 	 	LEAST(5, GREATEST(-1, YEARWEEK((SELECT IFNULL(t.FinishDate, i.FinishDate) FROM pm_Release i WHERE i.pm_ReleaseId = t.Release)) - YEARWEEK(NOW()))) ".
 			         " ) + 2 DueWeeks ";
 		
 		return $columns;
 	}
}

