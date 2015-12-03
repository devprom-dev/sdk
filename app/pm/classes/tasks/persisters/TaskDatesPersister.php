<?php

class TaskDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		 $columns = array();

		$columns[] =
			"  		IFNULL( t.PlannedFinishDate, ".
			"				IFNULL( ".
			"						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
			"						NULL ".
			"				) ".
			"			) DueDate ";

         $columns[] =
         	 "  GREATEST(0, TO_DAYS(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( t.PlannedFinishDate, ".
         	 "				IFNULL( ".
         	 "						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
             "						NULL ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - TO_DAYS(NOW())) DueDays ";

         $columns[] =
         	 "  LEAST(5, GREATEST(-1, YEARWEEK(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( t.PlannedFinishDate, ".
         	 "				IFNULL( ".
         	 "						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
             "						NULL ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - YEARWEEK(NOW()))) + 2 DueWeeks ";
 		
 		return $columns;
 	}
}

