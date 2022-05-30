<?php

class StageRegistry extends ObjectRegistrySQL
{
 	function getQueryClause(array $parms)
 	{
 	    $release = getFactory()->getObject('Release');
 	    $projectPrefix = getSession()->getProjectIt()->get('LinkedProject') != '';

 		$sql = " SELECT 
 		            LPAD(v.Caption, 8, '0') VersionNumber, 
 		            v.Caption Caption, ".
                    ($projectPrefix ? "CONCAT('{',(SELECT p.Caption FROM pm_Project p WHERE p.VPD = v.VPD),'} ')" : "''"). " CaptionPrefix, 
                    CONCAT('".translate('Релиз')." ', v.Caption) CaptionType, 
                    v.pm_VersionId pm_VersionId, 
                    NULL ParentStage, 
                    NULL ParentStageClass, 
                    DATE(v.EstimatedStartDate) EstimatedStartDate, 
                    DATE(v.EstimatedFinishDate) EstimatedFinishDate, 
                    DATE(v.StartDate) ActualStartDate, 
                    DATE(v.FinishDate) ActualFinishDate, 
                    DATE(v.StartDate) Deadlines, 
                    '' IterationDate,
                    '' RecentComment,
                    v.Description, 
                    v.Project, 
                    v.VPD, 
                    DATE(v.StartDate) StartDate, 
                    DATE(v.FinishDate) FinishDate, 
                    (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s 
                      WHERE s.PlannedRelease = v.pm_VersionId AND s.Iteration IS NULL ) Issues, 
                    (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s 
                      WHERE s.PlannedRelease = v.pm_VersionId) Increments, 
                    (SELECT COUNT(1) FROM pm_ChangeRequest s 
                      WHERE s.PlannedRelease = v.pm_VersionId AND s.FinishDate IS NULL ) UncompletedIssues, 
                    '' Tasks, 
                    0 UncompletedTasks, 
                    (SELECT GROUP_CONCAT(CAST(m.pm_ReleaseId AS CHAR)) FROM pm_Release m
                      WHERE m.Version = v.pm_VersionId) Iterations, 
                    v.RecordCreated, 
                    v.RecordModified, 
                    v.RecordVersion, 
                    CONCAT(LPAD(v.pm_VersionId, 8, '0'),LPAD('',8,'0')) Stage, 
                    (SELECT SUM(s.Estimation) FROM pm_ChangeRequest s 
                      WHERE s.PlannedRelease = v.pm_VersionId) IssuesPlanned, 
                    (SELECT SUM(s.Planned) FROM pm_Task s, pm_ChangeRequest r 
                      WHERE r.PlannedRelease = v.pm_VersionId AND r.pm_ChangeRequestId = s.ChangeRequest ) TasksPlanned, 
                    (SELECT SUM(a.Capacity) FROM pm_Activity a, pm_ChangeRequest s 
                      WHERE s.PlannedRelease = v.pm_VersionId AND a.Issue = s.pm_ChangeRequestId) IssuesFact, 
                    (SELECT SUM(a.Capacity) FROM pm_Activity a, pm_Task s, pm_ChangeRequest r 
                      WHERE r.PlannedRelease = v.pm_VersionId AND r.pm_ChangeRequestId = s.ChangeRequest AND a.Task = s.pm_TaskId ) TasksFact, 
                    'Release' State, 
                    (SELECT COUNT(1) FROM pm_Release r WHERE r.Version = v.pm_VersionId ) ChildrenCount,
                    v.IsClosed
               FROM (SELECT v.*,
                            IFNULL((SELECT DATE(MIN(m.MetricValueDate)) FROM pm_VersionMetric m 
                                     WHERE m.Version = v.pm_VersionId 
                                       AND m.Metric = 'EstimatedStart'), v.StartDate) EstimatedStartDate, 
                            IFNULL((SELECT DATE(MAX(m.MetricValueDate)) FROM pm_VersionMetric m 
                                     WHERE m.Version = v.pm_VersionId 
                                       AND m.Metric = 'EstimatedFinish'), v.FinishDate) EstimatedFinishDate
                       FROM pm_Version v WHERE 1 = 1 ".$release->getVpdPredicate('v').") v 
               WHERE 1 = 1 ";

        $milestone = getFactory()->getObject('Milestone');
        $milestoneTrace = getFactory()->getObject('RequestTraceMilestone');

        $sql .= " 
            UNION SELECT LPAD(t.Caption, 8, '0'), 
                t.Caption, ".
                ($projectPrefix ? "CONCAT('{',(SELECT p.Caption FROM pm_Project p WHERE p.VPD = t.VPD),'} ')" : "''"). ", 
                CONCAT('".translate('Веха')." ', t.Caption),
                t.pm_MilestoneId,
                NULL,
                NULL,
                DATE(t.MilestoneDate),
                DATE(t.MilestoneDate),
                DATE(t.MilestoneDate),
                DATE(t.MilestoneDate),
                DATE(t.MilestoneDate),
                '',
                '',
                t.Description,
                (SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD),
                t.VPD,
                DATE(t.MilestoneDate),
                DATE(t.MilestoneDate),
                ( SELECT GROUP_CONCAT(CAST(l.ChangeRequest AS CHAR)) FROM pm_ChangeRequestTrace l 
                   WHERE l.ObjectId = t.pm_MilestoneId AND l.ObjectClass = '".$milestoneTrace->getObjectClass()."' ),
                ( SELECT GROUP_CONCAT(CAST(l.ChangeRequest AS CHAR)) FROM pm_ChangeRequestTrace l 
                   WHERE l.ObjectId = t.pm_MilestoneId AND l.ObjectClass = '".$milestoneTrace->getObjectClass()."' ),
                0,
                '' Tasks,
                0,
                NULL,
                t.RecordCreated,
                t.RecordModified,
                t.RecordVersion,
                CONCAT(LPAD(t.pm_MilestoneId, 8, '0'),LPAD('',8,'0')),
                0,
                0,
                0,
                0,
                'Milestone' State,
                0,
                'N'
           FROM pm_Milestone t
          WHERE 1 = 1 " . $milestone->getVpdPredicate('t');

 		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() ) return "(".$sql.")";
 		
 		$iteration = getFactory()->getObject('Iteration');

		$sql .= 			   
		   " UNION 
		     SELECT IF(v.Caption IS NULL, LPAD(r.Caption,8,'0'), concat(LPAD(v.Caption,8,'0'), '.', LPAD(r.Caption,8,'0'))), 
		            IF(v.Caption IS NULL, r.Caption, concat(v.Caption, '.', r.Caption)), " .
                    ($projectPrefix ? "CONCAT('{',(SELECT p.Caption FROM pm_Project p WHERE p.VPD = r.VPD),'} ')" : "''"). " CaptionPrefix, 
                    IF(v.Caption IS NULL, CONCAT('".translate('Итерация')." ',r.Caption), CONCAT('".translate('Итерация')." ',v.Caption,'.',r.Caption)), 
                    r.pm_ReleaseId, 
                    v.pm_VersionId, 
                    IF(r.Version IS NULL, NULL, 'Release'), 
                    DATE(r.EstimatedStartDate), 
                    DATE(r.EstimatedFinishDate), 
                    DATE(r.StartDate) ActualStartDate, 
                    DATE(r.FinishDate) ActualFinishDate, 
                    DATE(r.StartDate) Deadlines, 
                    DATE(r.StartDate) IterationDate,
                    '' RecentComment,
                    r.Description, 
                    r.Project, 
                    r.VPD, 
                    DATE(r.StartDate), 
                    DATE(r.FinishDate), 
                    (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a 
                      WHERE a.Iteration = r.pm_ReleaseId) Issues, 
                    (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a 
                      WHERE a.Iteration = r.pm_ReleaseId) Increments, 
                    (SELECT COUNT(1) FROM pm_ChangeRequest s 
                      WHERE s.Iteration = r.pm_ReleaseId AND s.FinishDate IS NULL ) UncompletedIssues, 
                    (SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s 
                      WHERE r.pm_ReleaseId = s.Release ) Tasks, 
                    (SELECT COUNT(1) FROM pm_Task s 
                      WHERE r.pm_ReleaseId = s.Release AND s.FinishDate IS NULL) UncompletedTasks, 
                    r.pm_ReleaseId Iterations, 
                    r.RecordCreated, 
                    r.RecordModified, 
                    r.RecordVersion, 
                    CONCAT(LPAD(IFNULL(v.pm_VersionId, 0), 8, '0'),LPAD(r.pm_ReleaseId, 8, '0')) Stage, 
                    (SELECT SUM(s.Estimation) FROM pm_ChangeRequest s 
                      WHERE s.Iteration = r.pm_ReleaseId) IssuesPlanned, 
                    (SELECT SUM(s.Planned) FROM pm_Task s 
                      WHERE s.Release = r.pm_ReleaseId ) TasksPlanned, 
                    (SELECT SUM(a.Capacity) FROM pm_Activity a, pm_ChangeRequest s 
                      WHERE s.Iteration = r.pm_ReleaseId AND a.Issue = s.pm_ChangeRequestId) IssuesFact, 
                    (SELECT SUM(a.Capacity) FROM pm_Activity a, pm_Task s 
                      WHERE s.Release = r.pm_ReleaseId AND a.Task = s.pm_TaskId ) TasksFact, 
                    'Iteration' State, 
                    0 ChildrenCount,
                    r.IsClosed
               FROM (SELECT r.*, 
                            IFNULL((SELECT DATE(MIN(m.MetricValueDate)) FROM pm_IterationMetric m 
                                     WHERE m.Iteration = r.pm_ReleaseId 
                                       AND m.Metric = 'EstimatedStart'), r.StartDate) EstimatedStartDate, 
                            IFNULL((SELECT DATE(MAX(m.MetricValueDate)) FROM pm_IterationMetric m 
                                     WHERE m.Iteration = r.pm_ReleaseId 
                                       AND m.Metric = 'EstimatedFinish'), r.FinishDate) EstimatedFinishDate
                       FROM pm_Release r 
                      WHERE 1 = 1 ".$iteration->getVpdPredicate('r').") r 
                       LEFT OUTER JOIN pm_Version v ON v.pm_VersionId = r.Version ";
 	    
 	    return "(".$sql.")";
 	}
}