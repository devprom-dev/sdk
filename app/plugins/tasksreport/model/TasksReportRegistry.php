<?php

class TasksReportRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
    {
        return " (
            SELECT t.*, r.StartDateOnly, (SELECT MAX(ac.ReportDate) FROM pm_Activity ac WHERE ac.Task = t.pm_TaskId) LastDate, 
                   r.regionCaption, r.DayFact, r.FactRegion, r.regionId
              FROM pm_Task t INNER JOIN 
                   (select a.Task, cal.StartDateOnly, r.regionCaption, r.regionId, 
                           SUM(a.Capacity) DayFact,
                           ROUND(SUM(a.Capacity) / IF(av.StringValue = '1', 10, LENGTH(av.StringValue) - LENGTH(REPLACE(av.StringValue, ',', '')) + 1), 2) FactRegion
                      from pm_CalendarInterval cal, pm_Activity a, pm_CustomAttribute ca, pm_AttributeValue av,
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
                     where a.ReportDate = cal.StartDateOnly
                       and cal.Kind = 'day'
                       and av.ObjectId = a.Task
                       and av.CustomAttribute = ca.pm_CustomAttributeId
                       and ca.ReferenceName = '".REGION_REFNAME."'
                       and (FIND_IN_SET(r.regionId, av.StringValue) > 0 OR av.StringValue = '1')
                     group by 1, 2, 3, 4
                   ) r ON r.Task = t.pm_TaskId ) ";
    }
}