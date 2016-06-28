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

 		return $columns;
 	}
}
