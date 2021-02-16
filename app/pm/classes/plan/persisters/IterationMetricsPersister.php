<?php

class IterationMetricsPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array (
			'EstimatedStartDate',
			'EstimatedFinishDate',
			'Velocity'
		);
	}

	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$objectPK = $this->getPK($alias);
 		
 		$columns[] = 
 			"(SELECT DATE(MIN(m.MetricValueDate)) " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'EstimatedStart') EstimatedStartDate ";

 		$columns[] = 
 			"(SELECT DATE(MAX(m.MetricValueDate)) " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'EstimatedFinish') EstimatedFinishDate ";

 		$columns[] = 
 			"(SELECT m.MetricValue " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'Velocity' LIMIT 1) Velocity ";

		$columns[] =
			"(SELECT m.PlannedWorkload " .
			"   FROM pm_ReleaseMetrics m " .
			"  WHERE m.Release = ".$objectPK.
			"    AND m.TaskType IS NULL ".
			"  ORDER BY m.SnapshotDays ASC LIMIT 1) PlannedWorkload ";

		$columns[] =
			"(SELECT m.PlannedEstimation " .
			"   FROM pm_ReleaseMetrics m " .
			"  WHERE m.Release = ".$objectPK.
			"    AND m.TaskType IS NULL ".
			"  ORDER BY m.SnapshotDays ASC LIMIT 1) PlannedEstimation ";

        $columns[] =
            "((SELECT COUNT(1) FROM pm_Task s, pm_Methodology m 
	           WHERE s.Release = " .$objectPK."	 
	             AND m.VPD = ".$alias.".VPD
	             AND m.IsTasks = 'Y'
	             AND s.FinishDate IS NULL ) +
	          (SELECT COUNT(1) FROM pm_ChangeRequest s 
	           WHERE s.Iteration = " .$objectPK."	 
	             AND s.FinishDate IS NULL )) UncompletedItems ";

        return $columns;
 	}
}
