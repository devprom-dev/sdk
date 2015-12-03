<?php
class RequestDueDatesPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

		 $columns[] =
			 "  		IFNULL( ".
			 "				(SELECT MIN(ms.MilestoneDate) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
			 "		  		WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
			 "					AND tr.ObjectId = ms.pm_MilestoneId ".
			 "		 			AND IFNULL(ms.Passed, 'N') = 'N' ".
			 "					AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'), ".
			 "				IFNULL( ".
			 "						(SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
			 "						NULL ".
			 "				) ".
			 "			) DueDate ";

         $columns[] =
         	 "  GREATEST(0, TO_DAYS(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( ". 
         	 "				(SELECT MIN(ms.MilestoneDate) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
         	 "		  		WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
         	 "					AND tr.ObjectId = ms.pm_MilestoneId ".
         	 "		 			AND IFNULL(ms.Passed, 'N') = 'N' ".
         	 "					AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'), ".
         	 "				IFNULL( ".
         	 "						(SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
             "						NULL ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - TO_DAYS(NOW())) DueDays ";

         $columns[] =
         	 "  LEAST(5, GREATEST(-1, YEARWEEK(IFNULL( t.FinishDate, ". 
         	 "  		IFNULL( ". 
         	 "				(SELECT MIN(ms.MilestoneDate) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
         	 "		  		WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
         	 "					AND tr.ObjectId = ms.pm_MilestoneId ".
         	 "		 			AND IFNULL(ms.Passed,'N') = 'N' ".
         	 "					AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'), ".
         	 "				IFNULL( ".
         	 "						(SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
             "						NULL ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - YEARWEEK(NOW()))) + 2 DueWeeks ";
         
         return $columns;
     }
}
