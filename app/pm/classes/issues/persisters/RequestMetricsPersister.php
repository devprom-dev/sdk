<?php

class RequestMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

         $defaultDeliveryDate = " NULL ";
         $defaultDeliveryDateMethod = " NULL ";

         if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() || getSession()->getProjectIt()->getMethodologyIt()->HasReleases() ) {
             $defaultDeliveryDate =
                 "IFNULL( ".
                 "   (SELECT MAX(i.FinishDate) FROM pm_Release i, pm_Task s WHERE i.pm_ReleaseId = s.Release AND s.ChangeRequest = t.pm_ChangeRequestId), ".
                 "      IFNULL( ".
                 "          (SELECT MAX(i.FinishDate) FROM pm_Release i WHERE i.pm_ReleaseId = t.Iteration), ".
                 "              IFNULL( ".
                 " 				    (SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
                 "					     ".$defaultDeliveryDate.
                 "				) ".
                 "		)".
                 ")";
             $defaultDeliveryDateMethod =
                 "IFNULL( ".
                 "   (SELECT MAX(2) FROM pm_Release i, pm_Task s WHERE i.pm_ReleaseId = s.Release AND s.ChangeRequest = t.pm_ChangeRequestId), ".
                 "      IFNULL( ".
                 "          (SELECT MAX(2) FROM pm_Release i WHERE i.pm_ReleaseId = t.Iteration), ".
                 "              IFNULL( ".
                 " 				    (SELECT MAX(2) FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
                 "					     ".$defaultDeliveryDateMethod.
                 "				) ".
                 "		)".
                 ")";
         }
         else if ( $this->getObject()->getAttributeType('LeadTimeSLA') != '' ) {
             $defaultDeliveryDate =
                 " IFNULL( FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) + t.Estimation * 3600), ".$defaultDeliveryDate." )";
             $defaultDeliveryDateMethod =
                 " IF( t.Estimation IS NOT NULL, 3, ".$defaultDeliveryDate." )";
         }
         else {
             $defaultDeliveryDate = "(SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) + m.Rating * 3600) FROM pm_Project m WHERE m.VPD = t.VPD)";
             $defaultDeliveryDateMethod = "1";
         }

         $columns[] =
         	 "  IFNULL( t.FinishDate, ". 
             "          IFNULL( (SELECT MIN(ms.MilestoneDate) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
             "		  		      WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
             "					    AND tr.ObjectId = ms.pm_MilestoneId ".
             "					    AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'),".
         	 "				IFNULL( ".
         	 "					(SELECT MAX(r.DeliveryDate) ".
         	 "				   	   FROM pm_ChangeRequestLink l, pm_ChangeRequestLinkType lt, pm_ChangeRequest r ".
         	 "				  	  WHERE l.SourceRequest = t.pm_ChangeRequestId ".
         	 "				    	AND l.TargetRequest = r.pm_ChangeRequestId ".
       		 "						AND l.LinkType = lt.pm_ChangeRequestLinkTypeId ".
             "                      AND r.FinishDate IS NULL ".
         	 "						AND lt.ReferenceName IN ('implemented','blocked') ), ".
         	 "					".$defaultDeliveryDate.
         	 "				) ".
             " 	 		)  ".
             "	) MetricDeliveryDate ";

         $columns[] =
             "  IF( t.FinishDate IS NOT NULL, 4, ".
             "          IFNULL( (SELECT MAX(5) FROM pm_ChangeRequestTrace tr, pm_Milestone ms ".
             "		  		      WHERE tr.ChangeRequest = t.pm_ChangeRequestId ".
             "					    AND tr.ObjectId = ms.pm_MilestoneId ".
             "					    AND tr.ObjectClass = '".getFactory()->getObject('RequestTraceMilestone')->getObjectClass()."'),".
             "				IFNULL( ".
             "					(SELECT MAX(6) ".
             "				   	   FROM pm_ChangeRequestLink l, pm_ChangeRequestLinkType lt, pm_ChangeRequest r ".
             "				  	  WHERE l.SourceRequest = t.pm_ChangeRequestId ".
             "				    	AND l.TargetRequest = r.pm_ChangeRequestId ".
             "						AND l.LinkType = lt.pm_ChangeRequestLinkTypeId ".
             "                      AND r.FinishDate IS NULL ".
             "						AND lt.ReferenceName IN ('implemented','blocked') ), ".
             "					".$defaultDeliveryDateMethod.
             "				) ".
             " 	 		)  ".
             "	) MetricDeliveryDateMethod ";

		 $columns[] =
			 "  (SELECT CONCAT_WS(':',IFNULL(SUM(a.Capacity),0),GROUP_CONCAT(DISTINCT CAST(a.Task AS CHAR)))
			      FROM pm_Activity a, pm_Task s
                 WHERE s.ChangeRequest = t.pm_ChangeRequestId
			       AND s.pm_TaskId = a.Task) MetricSpentHoursData ";

         return $columns;
     }
}
