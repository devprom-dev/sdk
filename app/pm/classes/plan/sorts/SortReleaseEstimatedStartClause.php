<?php

class SortReleaseEstimatedStartClause extends SortClauseBase
{
 	function clause()
 	{
 		return "(SELECT MAX(m.MetricValueDate) " .
			   "   FROM pm_VersionMetric m" .
			   "  WHERE m.Version = ".$this->getAlias().".pm_VersionId" .
			   "	AND m.Metric = 'EstimatedStart' )";
 	} 	
}
