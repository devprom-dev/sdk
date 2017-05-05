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
				$taskStates = getFactory()->getObject('pm_Task')->getNonTerminalStates();
                $issueStates = getFactory()->getObject('pm_ChangeRequest')->getNonTerminalStates();

				return " AND EXISTS (SELECT 1 " .
					   "			   FROM pm_IterationMetric me " .
					   "			  WHERE me.Iteration = t.pm_ReleaseId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date('Y-m-d')."' > DATE(GREATEST(me.MetricValueDate, t.FinishDate)))" .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_Task s " .
			   		   "			       WHERE s.Release = t.pm_ReleaseId" .
			   		   "				     AND s.State IN ('".join("','",$taskStates)."'))".
                       " AND NOT EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.State IN ('".join("','",$issueStates)."'))";

 			case self::CURRENT:
 			    $taskStates = getFactory()->getObject('pm_Task')->getTerminalStates();
                $issueStates = getFactory()->getObject('pm_ChangeRequest')->getTerminalStates();

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
			   		   "				     AND s.State NOT IN ('".join("','",$taskStates)."'))".
                       "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.State NOT IN ('".join("','",$issueStates)."')) )";

 			case self::NOTPASSED:
                $taskStates = getFactory()->getObject('pm_Task')->getTerminalStates();
                $issueStates = getFactory()->getObject('pm_ChangeRequest')->getTerminalStates();

				return " AND (EXISTS (SELECT 1 " .
					   "			   FROM pm_IterationMetric me " .
					   "			  WHERE me.Iteration = t.pm_ReleaseId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date('Y-m-d')."' <= DATE(GREATEST(me.MetricValueDate, t.FinishDate)))" .
                       "      OR EXISTS ( SELECT 1 FROM pm_Task s " .
                       "			       WHERE s.Release = t.pm_ReleaseId" .
                       "				     AND s.State NOT IN ('".join("','",$taskStates)."'))".
                       "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest s " .
                       "			       WHERE s.Iteration = t.pm_ReleaseId" .
                       "				     AND s.State NOT IN ('".join("','",$issueStates)."')) )";
				
		    default:
				return " AND 1 = 2 ";
 		}
 	}
}
