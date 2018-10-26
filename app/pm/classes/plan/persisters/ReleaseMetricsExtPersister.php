<?php

class ReleaseMetricsExtPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
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

 		$columns[] =
	       " (SELECT COUNT(1) FROM pm_ChangeRequest s " .
	       "   WHERE s.PlannedRelease = " .$this->getPK($alias).
	       "	 AND s.FinishDate IS NULL) UncompletedItems ";
 		
 		return $columns;
 	}
}
