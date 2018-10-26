<?php

class ProjectDashboardPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array(
            'Features',
            'SpentHours',
            'SpentHoursWeek'
        );
    }

    function getSelectColumns( $alias )
    {
 		return array(
 			" ( SELECT GROUP_CONCAT(CAST(f.pm_FunctionId AS CHAR))
 			      FROM pm_Function f
 			     WHERE f.VPD = t.VPD
 			       AND EXISTS (SELECT 1 FROM pm_ChangeRequest r 
 			                    WHERE r.Function = f.pm_FunctionId 
 			                      AND r.StartDate IS NOT NULL
 			                      AND r.FinishDate IS NULL) ) Features ",

            " ( SELECT SUM(a.Capacity) FROM pm_Activity a
                 WHERE a.VPD = t.VPD ) SpentHours ",

            " ( SELECT SUM(a.Capacity) FROM pm_Activity a
                 WHERE a.VPD = t.VPD 
                   AND TO_DAYS(NOW()) - TO_DAYS(a.ReportDate) < 7  ) SpentHoursWeek ",

            " ( SELECT COUNT(1) FROM pm_ChangeRequest r WHERE r.Project = t.pm_ProjectId ) IssuesTotal",

            " ( SELECT COUNT(1) FROM pm_ChangeRequest r 
                 WHERE r.Project = t.pm_ProjectId 
                   AND r.FinishDate IS NOT NULL ) IssuesCompleted",

            " ( SELECT COUNT(1) FROM pm_Task r WHERE r.VPD = t.VPD ) TasksTotal",

            " ( SELECT COUNT(1) FROM pm_Task r 
                 WHERE r.VPD = t.VPD 
                   AND r.FinishDate IS NOT NULL ) TasksCompleted"
 		);
 	}
 }
