<?php

class ReleaseMetricsExtPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    global $model_factory;
 	    
 		$columns = array();

 		$columns[] =  
 			"(SELECT m.MetricValue " .
			"   FROM pm_VersionMetric m" .
			"  WHERE m.Version = " .$this->getPK($alias).
			"	 AND m.Metric = 'Workload' LIMIT 1) Workload ";

 		$columns[] =  
 			"(SELECT m.MetricValue " .
			"   FROM pm_VersionMetric m" .
			"  WHERE m.Version = " .$this->getPK($alias).
			"	 AND m.Metric = 'Estimation' LIMIT 1) Estimation ";

		$issue = $model_factory->getObject('pm_ChangeRequest');
		
		$states = $issue->getNonTerminalStates();
 		
 		$columns[] =  
	       " (SELECT COUNT(1) FROM pm_ChangeRequest s " .
	       "   WHERE s.PlannedRelease = " .$this->getPK($alias).
	       "	 AND s.State IN ('".join("','",$states)."')) UncompletedItems ";
 		
 		return $columns;
 	}
}
