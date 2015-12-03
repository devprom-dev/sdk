<?php

class ProjectMetricRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
        $default_metric = '';
        foreach( $this->getFilters() as $filter ) {
            if ( $filter instanceof FilterAttributePredicate ) {
                if ( $filter->getAttribute() == 'Metric' ) {
                    $default_metric = $filter->getValue();
                }
            }
        }
		return " (SELECT t.pm_ProjectMetricId,
		 				 IFNULL(t.Metric, '$default_metric') Metric,
		 				 t.MetricValue,
		 				 IFNULL(t.RecordCreated,i.StartDate) RecordCreated,
		 				 IFNULL(t.RecordModified,i.StartDate) RecordModified,
		 				 t.RecordVersion,
		 				 UNIX_TIMESTAMP(i.StartDateOnly) DayDate,
						 IFNULL(t.Project, ".getSession()->getProjectIt()->getId().") Project,
		 				 IFNULL(t.VPD, '".getSession()->getProjectIt()->get('VPD')."') VPD
					FROM pm_CalendarInterval i LEFT OUTER JOIN pm_ProjectMetric t ON i.StartDateOnly = DATE(t.RecordCreated)
				   WHERE i.Kind = 'day' AND i.StartDate <= NOW()
				  ) ";
	}

    function setMetric( $metric, $value )
    {
        $this->setLimit(1);
        $row_it = $this->Query(
            array (
                new FilterAttributePredicate('Metric', $metric),
                new FilterAttributePredicate('Project', getSession()->getProjectIt()->getId()),
                new SortRecentClause()
            )
        );
        DAL::Instance()->Query("
            REPLACE INTO pm_ProjectMetric (pm_ProjectMetricId, Project, VPD, Metric, MetricValue, RecordModified, RecordCreated)
              VALUES ('".$row_it->getId()."', ".getSession()->getProjectIt()->getId().", '".getSession()->getProjectIt()->get('VPD')."', '".$metric."', '".$value."', NOW(), NOW())
        ");
    }
}