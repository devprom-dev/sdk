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
 			"(SELECT m.MetricValueDate " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'EstimatedStart' LIMIT 1) EstimatedStartDate ";

 		$columns[] = 
 			"(SELECT m.MetricValueDate " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'EstimatedFinish' LIMIT 1) EstimatedFinishDate ";

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

 		return $columns;
 	}
}
