<?php

class PlanChartDataRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
        $items = array(
            " SELECT IFNULL(t.MilestoneDate, NOW()) StartDate, t.MilestoneDate FinishDate, ".
            "		 (SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD) Project, ".
            "		 t.Caption, t.pm_MilestoneId entityId, 'Milestone' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, t.MilestoneDate EstimatedFinishDate, ".
            "		 'Milestone' ObjectType, 0 SortIndex, '' Custom1, 0 UncompletedTasks, 0 UncompletedIssues ".
            "   FROM pm_Milestone t ".
            "  WHERE 1 = 1 ".getFactory()->getObject('Milestone')->getVpdPredicate('t'),

			" SELECT IFNULL(t.StartDate, NOW()) StartDate, IFNULL(t.FinishDate,DATE_ADD(NOW(),INTERVAL 3 YEAR)) FinishDate, t.Project, t.Caption, ".
			"		 t.pm_VersionId entityId, 'Release' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, ".
            "        IFNULL((SELECT m.MetricValueDate FROM pm_VersionMetric m " .
            "		  			     WHERE m.Version = t.pm_VersionId " .
            "						   AND m.Metric = 'EstimatedFinish' LIMIT 1), t.FinishDate) EstimatedFinishDate, ".
			"		 'Release' ObjectType, t.Caption SortIndex, '' Custom1,".
            "        (SELECT COUNT(1) FROM pm_ChangeRequest s " .
            "	       WHERE s.PlannedRelease = t.pm_VersionId" .
            "			 AND s.FinishDate IS NULL) UncompletedIssues, ".
            "        0 UncompletedTasks ".
            "   FROM pm_Version t ".
 			"  WHERE 1 = 1 ".getFactory()->getObject('Release')->getVpdPredicate('t'),

			" SELECT t.StartDate, t.FinishDate, t.Project, t.ReleaseNumber, ".
			"		 t.pm_ReleaseId entityId, 'Iteration' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, ".
            "		 IFNULL((SELECT m.MetricValueDate FROM pm_IterationMetric m " .
            "		  			     WHERE m.Iteration = t.pm_ReleaseId " .
            "						   AND m.Metric = 'EstimatedFinish' LIMIT 1), t.FinishDate) EstimatedFinishDate, " .
			"		 'Release' ObjectType, t.ReleaseNumber SortIndex, ".
		    "		 TO_DAYS(t.StartDate) - IFNULL(TO_DAYS((SELECT r.StartDate FROM pm_Version r WHERE r.pm_VersionId = t.Version AND r.VPD = t.VPD)), 0) Custom1,".
            "         0 UncompletedIssues, ".
            "        (SELECT COUNT(1) FROM pm_Task s " .
            "	      WHERE t.pm_ReleaseId = s.Release ".
            "			AND s.FinishDate IS NULL) UncompletedTasks ".
 			"   FROM pm_Release t ".
 			"  WHERE 1 = 1 ".getFactory()->getObject('Iteration')->getVpdPredicate('t'),
				
		);
 		
 	    return "(".join(" UNION ", $items).")";
 	}
}