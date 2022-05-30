<?php

class RequestMetricsFactPersister extends ObjectSQLPersister
{
     function getSelectColumns( $alias )
     {
         $columns = array();

		 $columns[] =
			 "  (SELECT CONCAT_WS(':',IFNULL(SUM(a.Capacity),0),GROUP_CONCAT(DISTINCT CAST(a.Task AS CHAR)))
			      FROM pm_Activity a, pm_Task s
                 WHERE s.ChangeRequest = t.pm_ChangeRequestId
			       AND s.pm_TaskId = a.Task ) MetricSpentHoursData ";

         $columns[] =
             "  (SELECT IFNULL(SUM(a.Capacity),0)
			      FROM pm_Activity a, pm_Task s
                 WHERE s.ChangeRequest = t.pm_ChangeRequestId
			       AND s.pm_TaskId = a.Task
			       AND a.ReportDate = CURDATE() ) MetricSpentHoursToday ";

         return $columns;
     }
}
