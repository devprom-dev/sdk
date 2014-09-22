<?php

class SortFeatureStartClause extends SortClauseBase
{
 	function clause()
 	{
 		return "(SELECT MAX(m.MetricValueDate) " .
			   "   FROM pm_ChangeRequest r, pm_VersionMetric m" .
			   "  WHERE r.Function = ".$this->getAlias().".pm_FunctionId" .
			   "	AND r.PlannedRelease = m.Version" .
			   "	AND m.Metric = 'EstimatedStart' )";
 	} 	
}
