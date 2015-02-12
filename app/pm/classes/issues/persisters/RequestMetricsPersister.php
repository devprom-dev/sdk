<?php

class RequestMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();
         
         $columns[] =
         	 "  IFNULL( t.FinishDate, ". 
         	 "  	IFNULL( ". 
         	 "			(SELECT MIN(ms.MilestoneDate) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
         	 "		  	WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
         	 "				AND tr.ObjectId = ms.pm_MilestoneId ".
         	 "		 		AND ms.Passed = 'N' ".
         	 "				AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'), ".
         	 "			IFNULL( ".
         	 "				(SELECT r.DeliveryDate ".
         	 "			   	   FROM pm_ChangeRequestLink l, pm_ChangeRequestLinkType lt, pm_ChangeRequest r ".
         	 "			  	  WHERE l.SourceRequest = t.pm_ChangeRequestId ".
         	 "			    	AND l.TargetRequest = r.pm_ChangeRequestId ".
       		 "					AND l.LinkType = lt.pm_ChangeRequestLinkTypeId ".
         	 "					AND lt.ReferenceName = 'implemented' ), ".
         	 "				IFNULL( ".
         	 "					(SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
             "					(SELECT FROM_DAYS(TO_DAYS(GREATEST(IFNULL(t.StartDate,t.RecordCreated), NOW())) ".
             "								+ IF(IFNULL(pr.Rating,0) <= 0, 0, GREATEST(0, ROUND(IFNULL(t.Estimation, 1) / pr.Rating, 1)))) ".
             "	   	   	  	   	   FROM pm_Project pr " .
             "	  	  	  	  	  WHERE t.VPD = pr.VPD) ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) MetricDeliveryDate ";
             
         return $columns;
     }
}
