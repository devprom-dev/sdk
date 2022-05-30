<?php

class IterationRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
	{
	    return " (
   	      SELECT t.*,
                 DATE(t.StartDate) StartDateOnly, 
                 DATE(t.FinishDate) FinishDateOnly, 
                 DATE(GREATEST(NOW(), t.StartDate)) AdjustedStart, 
                 DATE(LEAST(GREATEST(NOW(), t.StartDate), 
                    (SELECT DATE(MIN(m.MetricValueDate)) 
                       FROM pm_IterationMetric m 
                      WHERE m.Iteration = t.pm_ReleaseId 
                        AND m.Metric = 'EstimatedFinish'))) AdjustedFinish 
            FROM pm_Release t 
           WHERE 1 = 1 {$this->getFilterPredicate($this->extractPredicates($parms),'t')}) ";
	}
}