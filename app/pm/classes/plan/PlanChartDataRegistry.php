<?php

class PlanChartDataRegistry extends ObjectRegistrySQL
{
 	function getQueryClause(array $parms)
 	{
        $items = array(
            " SELECT IFNULL(t.MilestoneDate, NOW()) StartDate,
                     IFNULL(t.MilestoneDate, NOW()) EstimatedStartDate,
                     DATE(t.MilestoneDate) FinishDate, 
                     DATE(t.MilestoneDate) EstimatedFinishDate,
                     (SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD) Project, 
                     t.Caption, t.pm_MilestoneId entityId, 'Milestone' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, 
                     'Milestone' ObjectType, 0 SortIndex, '' Custom1, 0 UncompletedTasks, 0 UncompletedIssues, 0 `Release`, 'N' IsClosed 
                FROM pm_Milestone t 
               WHERE 1 = 1 ".getFactory()->getObject('Milestone')->getVpdPredicate('t'),

			" SELECT t.StartDate,
                     IFNULL(
                         (SELECT DATE(MAX(m.MetricValueDate)) FROM pm_VersionMetric m 
			               WHERE m.Version = t.pm_VersionId 
			                 AND m.Metric = 'EstimatedStart'), t.StartDate
                         ) EstimatedStartDate, 
                     t.FinishDate,
                     IFNULL(
                         (SELECT DATE(MAX(m.MetricValueDate)) FROM pm_VersionMetric m 
			               WHERE m.Version = t.pm_VersionId 
			                 AND m.Metric = 'EstimatedFinish'), t.FinishDate
                         ) EstimatedFinishDate, 
                     t.Project, t.Caption, 
			         t.pm_VersionId entityId, 'Release' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, 
                     'Release' ObjectType, t.Caption SortIndex, '' Custom1,
                     (SELECT COUNT(1) FROM pm_ChangeRequest s 
                       WHERE s.PlannedRelease = t.pm_VersionId
                         AND s.FinishDate IS NULL) UncompletedIssues, 
                     0 UnscompletedTasks, t.pm_VersionId, t.IsClosed
                FROM pm_Version t WHERE 1 = 1 ".getFactory()->getObject('Release')->getVpdPredicate('t'),

			" SELECT t.StartDate,
                     IFNULL(
                        (SELECT DATE(MAX(m.MetricValueDate)) FROM pm_IterationMetric m 
			          	  WHERE m.Iteration = t.pm_ReleaseId 
			          	    AND m.Metric = 'EstimatedStart'), t.StartDate
                        ) EstimatedStartDate, 
                     t.FinishDate,
                     IFNULL(
                        (SELECT DATE(MAX(m.MetricValueDate)) FROM pm_IterationMetric m 
			          	  WHERE m.Iteration = t.pm_ReleaseId 
			          	    AND m.Metric = 'EstimatedFinish'), t.FinishDate
                        ) EstimatedFinishDate,
                     t.Project, t.Caption,  
			         t.pm_ReleaseId entityId, 'Iteration' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, 
			         'Release' ObjectType, t.Caption SortIndex, 
			         TO_DAYS(t.StartDate) - IFNULL(TO_DAYS((SELECT r.StartDate FROM pm_Version r WHERE r.pm_VersionId = t.Version AND r.VPD = t.VPD)), 0) Custom1,
			         (SELECT COUNT(1) FROM pm_ChangeRequest s 
			           WHERE s.Iteration = t.pm_ReleaseId
			             AND s.FinishDate IS NULL) UncompletedIssues, 
			         (SELECT COUNT(1) FROM pm_Task s  
			          WHERE t.pm_ReleaseId = s.Release   
			            AND s.FinishDate IS NULL) UncompletedTasks, t.Version, t.IsClosed  
                FROM pm_Release t WHERE 1 = 1 ".getFactory()->getObject('Iteration')->getVpdPredicate('t')
		);

 	    return "(".join(" UNION ", $items).")";
 	}
}