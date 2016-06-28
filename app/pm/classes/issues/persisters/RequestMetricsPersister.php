<?php

class RequestMetricsPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

		 $terminalIds = array();
		 $state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
		 while( !$state_it->end() ) {
			 if ( $state_it->get('IsTerminal') == 'Y' ) {
				 $terminalIds[] = $state_it->getId();
			 }
			 $state_it->moveNext();
		 }
		 if ( count($terminalIds) < 1 ) $terminalIds = array(0);

         $columns[] =
         	 "  IFNULL( t.FinishDate, ". 
         	 "  	IFNULL( (SELECT so.RecordCreated FROM pm_StateObject so WHERE so.pm_StateObjectId = t.StateObject AND so.State IN (".join(',',$terminalIds).")), ".
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
			 " 							(SELECT v.FinishDate FROM pm_Version v WHERE v.pm_VersionId = t.PlannedRelease), ".
             "							(SELECT FROM_DAYS(m.MetricValue) FROM pm_ProjectMetric m WHERE m.VPD = t.VPD AND m.Metric = 'EstimatedFinishDate' LIMIT 1) ".
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
