<?php

 class ProjectMetricDatesPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" (SELECT TO_DAYS(IFNULL(FROM_DAYS(TO_DAYS(GREATEST(IFNULL(MAX(pr.FinishDate),NOW()), NOW()))
 								+ IF(IFNULL(MAX(pr.Rating),0) <= 0, 0, GREATEST(0, ROUND(IFNULL(SUM(r.Estimation), 1) / MAX(pr.Rating), 1)))),NOW()))
 				 FROM pm_Project pr, pm_ChangeRequest r
 				WHERE t.VPD = pr.VPD
 				  AND r.Project = pr.pm_ProjectId
 				  AND r.PlannedRelease IS NULL
 				  AND NOT EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = r.pm_ChangeRequestId AND s.Release IS NOT NULL)
 				  AND r.FinishDate IS NULL) EstimatedFinishDate "
 		);
 	}
 }
