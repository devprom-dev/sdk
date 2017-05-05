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

        $columns[] =
            "(SELECT m.PlannedWorkload " .
            "   FROM pm_VersionBurndown m " .
            "  WHERE m.Version = ".$objectPK.
            "  ORDER BY m.SnapshotDays ASC LIMIT 1) PlannedWorkload ";

        $columns[] =
            " (SELECT COUNT(1) FROM pm_ChangeRequest s " .
            "   WHERE s.PlannedRelease = " .$objectPK.
            "	 AND s.State IN ('".join("','",getFactory()->getObject('pm_ChangeRequest')->getNonTerminalStates())."')) UncompletedItems ";

 		return $columns;
 	}
}
