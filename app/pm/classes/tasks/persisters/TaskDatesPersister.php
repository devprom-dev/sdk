<?php

class TaskDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		 $columns = array();
 		
         $columns[] =
         	 "  GREATEST(0, TO_DAYS(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( ". 
         	 "				(SELECT MIN(ms.MilestoneDate) FROM pm_TaskTrace tr, pm_Milestone ms ".
         	 "		  		  WHERE tr.Task = t.pm_TaskId ".
         	 "					AND tr.ObjectId = ms.pm_MilestoneId ".
         	 "		 			AND IFNULL(ms.Passed, 'N') = 'N' ".
         	 "					AND tr.ObjectClass = '".getFactory()->getObject('TaskTraceTask')->getObjectClass()."'), ".
         	 "				IFNULL( ".
         	 "						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
             "						DATE_ADD(NOW(), INTERVAL 365 DAY) ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - TO_DAYS(NOW())) DueDays ";

         $columns[] =
         	 "  LEAST(5, GREATEST(-1, YEARWEEK(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( ". 
         	 "				(SELECT MIN(ms.MilestoneDate) FROM pm_TaskTrace tr, pm_Milestone ms ".
         	 "		  		  WHERE tr.Task = t.pm_TaskId ".
         	 "					AND tr.ObjectId = ms.pm_MilestoneId ".
         	 "		 			AND IFNULL(ms.Passed,'N') = 'N' ".
         	 "					AND tr.ObjectClass = '".getFactory()->getObject('TaskTraceTask')->getObjectClass()."'), ".
         	 "				IFNULL( ".
         	 "						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
             "						DATE_ADD(NOW(), INTERVAL 365 DAY) ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - YEARWEEK(NOW()))) + 2 DueWeeks ";
 		
 		return $columns;
 	}
}

