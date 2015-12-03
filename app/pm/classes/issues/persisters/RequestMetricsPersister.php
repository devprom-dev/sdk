<?php

class RequestMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();
         $terminal = getFactory()->getObject('Request')->getTerminalStates();
         
         $columns[] =
         	 "  IFNULL( t.FinishDate, ". 
         	 "  	IFNULL( (SELECT IFNULL((SELECT so.RecordCreated FROM pm_StateObject so WHERE so.pm_StateObjectId = t.StateObject), t.RecordModified) ".
         	 "				   FROM pm_State st ".
         	 "				  WHERE st.ReferenceName = t.State ".
          	 "				    AND st.VPD = t.VPD	".
          	 "				    AND st.ObjectClass = 'request'	".
          	 "					AND st.IsTerminal = 'Y' LIMIT 1), ". 
         	 "				IFNULL( ".
         	 "					(SELECT MAX(r.DeliveryDate) ".
         	 "				   	   FROM pm_ChangeRequestLink l, pm_ChangeRequestLinkType lt, pm_ChangeRequest r ".
         	 "				  	  WHERE l.SourceRequest = t.pm_ChangeRequestId ".
         	 "				    	AND l.TargetRequest = r.pm_ChangeRequestId ".
       		 "						AND l.LinkType = lt.pm_ChangeRequestLinkTypeId ".
         	 "						AND lt.ReferenceName = 'implemented' ), ".
         	 "					IFNULL( ".
         	 "						(SELECT MAX(i.FinishDate) FROM pm_Release i, pm_Task s WHERE i.pm_ReleaseId = s.Release AND s.ChangeRequest = t.pm_ChangeRequestId), ".
         	 "						IFNULL( ".
         	 "							(SELECT v.FinishDate ".
         	 "					           FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
             "							(SELECT FROM_DAYS(TO_DAYS(GREATEST(pr.FinishDate, NOW())) ".
             "										+ IF(IFNULL(MAX(pr.Rating),0) <= 0, 0, GREATEST(0, ROUND(IFNULL(SUM(r.Estimation), 1) / MAX(pr.Rating), 1)))) ".
             "	   	   		  	   	   	   FROM pm_Project pr, pm_ChangeRequest r " .
             "	  	  		  	  	  	  WHERE t.VPD = pr.VPD ".
         	 "							    AND r.Project = pr.pm_ProjectId ".
         	 "							    AND r.PlannedRelease IS NULL ".
         	 "							    AND NOT EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = r.pm_ChangeRequestId AND s.Release IS NOT NULL) ".
         	 "							    AND r.State NOT IN ('".join("','", $terminal)."') ) ".
         	 "						) ".
             "					) ".
         	 "				) ".
             " 	 		)  ".
             "	) MetricDeliveryDate ";

		 $columns[] =
			 "  (SELECT CONCAT_WS(':',IFNULL(SUM(a.Capacity),0),GROUP_CONCAT(DISTINCT CAST(a.Task AS CHAR)))
			      FROM pm_Activity a, pm_Task s
                 WHERE s.ChangeRequest = t.pm_ChangeRequestId
			       AND s.pm_TaskId = a.Task) MetricSpentHoursData ";

         $columns[] =
             "  (SELECT CONCAT_WS(':',IFNULL(SUM(r.Fact),0),GROUP_CONCAT(r.FactTasks))
			      FROM pm_ChangeRequestLink l, pm_ChangeRequestLinkType lt, pm_ChangeRequest r
				 WHERE t.pm_ChangeRequestId = l.SourceRequest
				   AND l.TargetRequest = r.pm_ChangeRequestId
				   AND l.LinkType = lt.pm_ChangeRequestLinkTypeId
				   AND lt.ReferenceName = 'implemented') MetricSpentHoursParentData ";

         return $columns;
     }
}
