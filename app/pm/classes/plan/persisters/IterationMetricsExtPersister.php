<?php

class IterationMetricsExtPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    global $model_factory;
 	    
 		$columns = array();
 		
  		$objectPK = $this->getPK($alias);
 		
 		$columns[] = 
 			"(SELECT m.MetricValue " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'ReleaseEstimation' LIMIT 1) ReleaseEstimation ";

 		$columns[] = 
 			"(SELECT m.MetricValue " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'IterationEstimation' LIMIT 1) IterationEstimation ";
 		
 		$columns[] = 
 			"(SELECT m.MetricValue " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'Efficiency' LIMIT 1) Efficiency ";
 		
 		$columns[] = 
 			"(SELECT m.MetricValue " .
			"   FROM pm_IterationMetric m" .
			"  WHERE m.Iteration = " .$objectPK.
			"	 AND m.Metric = 'RequestEstimationError' LIMIT 1) RequestEstimationError ";
 		
		$task = $model_factory->getObject('pm_Task');
		
		$states = $task->getNonTerminalStates();
 		
 		$columns[] =  
	       " (SELECT COUNT(1) FROM pm_Task s " .
	       "   WHERE s.Release = " .$this->getPK($alias).
	       "	 AND s.State IN ('".join("','",$states)."')) UncompletedItems ";
 		
 		return $columns;
 	}
}
