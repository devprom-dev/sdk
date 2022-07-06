<?php
include "predicates/TasksReportActivityPredicate.php";

class TasksReportRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
    {
        $activityClause = "";
        foreach( $parms as $parameter ) {
            if ( $parameter instanceof \SpentTimeReportDatePredicate ) {
                $activityClause .= " AND a.ReportDate BETWEEN '{$parameter->getAfterDate()}' AND '{$parameter->getBeforeDate()}' ";
            }
            if ( $parameter instanceof \TasksReportActivityPredicate ) {
                $activityClause .= " AND a.pm_ActivityId IN ({$parameter->getValue()}) ";
            }
        }

        return " (
            SELECT t.*, 
                   r.ReportDate StartDateOnly, 
                   (SELECT MAX(a.ReportDate) FROM pm_Activity a WHERE a.Task = t.pm_TaskId {$activityClause}) LastDate, 
                   r.regionCaption, 
                   r.DayFact, 
                   r.FactRegion, 
                   r.regionId,
                   (SELECT SUM(a.Capacity) FROM pm_Activity a WHERE a.Task = t.pm_TaskId {$activityClause}) FactPeriod,
                   r.Activities
              FROM pm_Task t INNER JOIN
                   (select a.Task, a.ReportDate, r.regionCaption, r.regionId, av.StringValue,
                           SUM(a.Capacity) DayFact,
                           ROUND(SUM(a.Capacity) / IF(av.StringValue = '1', 10, LENGTH(av.StringValue) - LENGTH(REPLACE(av.StringValue, ',', '')) + 1), 2) FactRegion,
                           GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) Activities
                      from pm_Activity a, pm_CustomAttribute ca, pm_AttributeValue av,
                            (select 2 regionId, 'Великий Новгород' regionCaption
                             union
                             select 3, 'Воронеж'
                             union
                             select 4, 'Карелия'
                              union
                             select 5, 'Кубань'
                             union
                             select 6, 'Марий Эл'
                             union
                             select 7, 'Нижний Новгород'
                             union
                             select 8, 'Пенза'
                             union
                             select 9, 'Ростов-на-Дону'
                             union
                             select 10, 'Тула'
                             union
                             select 11, 'Ярославль' ) r
                     where av.ObjectId = a.Task 
                       and ca.EntityReferenceName = 'task' {$activityClause}
                       and av.CustomAttribute = ca.pm_CustomAttributeId
                       and ca.ReferenceName = '".REGION_REFNAME."'
                       and (FIND_IN_SET(r.regionId, av.StringValue) > 0 OR av.StringValue = '1')
                     group by 1, 2, 3, 4, 5
                   ) r ON t.pm_TaskId = r.Task
            UNION
            SELECT t.*, 
                   r.ReportDate, 
                   (SELECT MAX(a.ReportDate) FROM pm_Activity a WHERE a.Issue = t.ChangeRequest {$activityClause}) LastDate, 
                   r.regionCaption, 
                   r.DayFact, 
                   r.FactRegion, 
                   r.regionId,
                   (SELECT SUM(a.Capacity) FROM pm_Activity a WHERE a.Issue = t.ChangeRequest {$activityClause}) FactPeriod,
                   r.Activities
              FROM pm_Task t INNER JOIN
                   (select a.Issue, a.ReportDate, r.regionCaption, r.regionId, av.StringValue,
                           SUM(a.Capacity) DayFact,
                           ROUND(SUM(a.Capacity) / IF(av.StringValue = '1', 10, LENGTH(av.StringValue) - LENGTH(REPLACE(av.StringValue, ',', '')) + 1), 2) FactRegion,
                           GROUP_CONCAT(CAST(a.pm_ActivityId AS CHAR)) Activities
                      from pm_Activity a, pm_CustomAttribute ca, pm_AttributeValue av,
                            (select 2 regionId, 'Великий Новгород' regionCaption
                             union
                             select 3, 'Воронеж'
                             union
                             select 4, 'Карелия'
                              union
                             select 5, 'Кубань'
                             union
                             select 6, 'Марий Эл'
                             union
                             select 7, 'Нижний Новгород'
                             union
                             select 8, 'Пенза'
                             union
                             select 9, 'Ростов-на-Дону'
                             union
                             select 10, 'Тула'
                             union
                             select 11, 'Ярославль' ) r
                     where (av.ObjectId = a.Issue and a.Task IS NULL
                                or av.ObjectId in (SELECT ts.ChangeRequest FROM pm_Task ts WHERE ts.pm_TaskId = a.Task))  
                       and ca.EntityReferenceName in ('issue','request') {$activityClause} 
                       and av.CustomAttribute = ca.pm_CustomAttributeId
                       and ca.ReferenceName = '".REGION_REFNAME."'
                       and (FIND_IN_SET(r.regionId, av.StringValue) > 0 OR av.StringValue = '1')
                     group by 1, 2, 3, 4, 5
                   ) r ON t.ChangeRequest = r.Issue
           ) ";
    }
}