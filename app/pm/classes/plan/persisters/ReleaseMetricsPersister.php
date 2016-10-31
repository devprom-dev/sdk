<?php

class ReleaseMetricsPersister extends ObjectSQLPersister
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
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"(SELECT m.MetricValueDate " .
			"   FROM pm_VersionMetric m" .
			"  WHERE m.Version = " .$objectPK.
			"	 AND m.Metric = 'EstimatedStart' LIMIT 1) EstimatedStartDate " );

 		array_push( $columns, 
 			"(SELECT m.MetricValueDate " .
			"   FROM pm_VersionMetric m" .
			"  WHERE m.Version = " .$objectPK.
			"	 AND m.Metric = 'EstimatedFinish' LIMIT 1) EstimatedFinishDate " );

 		array_push( $columns, 
 			"(SELECT m.MetricValue " .
			"   FROM pm_VersionMetric m" .
			"  WHERE m.Version = " .$objectPK.
			"	 AND m.Metric = 'Velocity' LIMIT 1) Velocity " );
 		
 		return $columns;
 	}
}
