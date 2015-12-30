<?php

class IterationTimelinePredicate extends FilterPredicate
{
	const PAST = 'past';
	const CURRENT = 'current';
	const NOTPASSED = 'not-passed';
	
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case self::PAST:
				
				$states = getFactory()->getObject('pm_Task')->getNonTerminalStates();

				return " AND EXISTS (SELECT 1 " .
					   "			   FROM pm_IterationMetric me " .
					   "			  WHERE me.Iteration = t.pm_ReleaseId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date('Y-m-d')."' > DATE(GREATEST(me.MetricValueDate, t.FinishDate)))" .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_Task s " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
			   		   "				     AND s.State IN ('".join("','",$states)."'))";
				
 			case self::CURRENT:
				
 			    $states = getFactory()->getObject('pm_Task')->getTerminalStates();

				return " AND (EXISTS (SELECT 1 " .
					   "			   FROM pm_IterationMetric ms, pm_IterationMetric me " .
					   "			  WHERE ms.Iteration = t.pm_ReleaseId" .
					   "                AND ms.Metric = 'EstimatedStart' ".
					   "			    AND me.Iteration = t.pm_ReleaseId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date('Y-m-d')."' BETWEEN DATE(GREATEST(ms.MetricValueDate, t.StartDate)) ".
					   "							  AND DATE(GREATEST(me.MetricValueDate, t.FinishDate)) )" .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_Task s " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
			   		   "				     AND s.State NOT IN ('".join("','",$states)."')))";

 			case self::NOTPASSED:
				return " AND (EXISTS (SELECT 1 " .
					   "			   FROM pm_IterationMetric me " .
					   "			  WHERE me.Iteration = t.pm_ReleaseId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date('Y-m-d')."' <= DATE(GREATEST(me.MetricValueDate, t.FinishDate)))" .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_Task s " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
			   		   "				     AND s.State NOT IN ('".join("','",getFactory()->getObject('pm_Task')->getTerminalStates())."')))";
				
		    default:
				return " AND 1 = 2 ";
 		}
 	}
}
