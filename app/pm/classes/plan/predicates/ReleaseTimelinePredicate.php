<?php

class ReleaseTimelinePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'past':
				
				$states = getFactory()->getObject('pm_ChangeRequest')->getNonTerminalStates();
 			    
 			    return " AND EXISTS (SELECT 1 " .
					   "			   FROM pm_VersionMetric ms, pm_VersionMetric me " .
					   "			  WHERE ms.Version = t.pm_VersionId" .
					   "                AND ms.Metric = 'EstimatedStart' ".
					   "			    AND me.Version = t.pm_VersionId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date()."' > GREATEST(me.MetricValueDate, t.FinishDate) )" .
			   		   " AND NOT EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.State IN ('".join("','",$states)."'))";
					   
			case 'current':
			    
				$states = getFactory()->getObject('pm_ChangeRequest')->getTerminalStates();

				return " AND (EXISTS (SELECT 1 " .
					   "			   FROM pm_VersionMetric ms, pm_VersionMetric me " .
					   "			  WHERE ms.Version = t.pm_VersionId" .
					   "                AND ms.Metric = 'EstimatedStart' ".
					   "			    AND me.Version = t.pm_VersionId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date()."' BETWEEN GREATEST(ms.MetricValueDate, t.StartDate) ".
					   "							  AND GREATEST(IFNULL(me.MetricValueDate, NOW()), IFNULL(t.FinishDate, NOW())) )" .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.State NOT IN ('".join("','",$states)."')))";

			case 'not-passed':
			    
				$states = getFactory()->getObject('pm_ChangeRequest')->getTerminalStates();
				
				return " AND (EXISTS (SELECT 1 " .
					   "			   FROM pm_VersionMetric me " .
					   "			  WHERE me.Version = t.pm_VersionId ".
					   "                AND me.Metric = 'EstimatedFinish' " .
					   "			    AND '".SystemDateTime::date()."' <= GREATEST(IFNULL(me.MetricValueDate, NOW()), IFNULL(t.FinishDate, NOW())) )" .
			   		   "      OR EXISTS ( SELECT 1 FROM pm_ChangeRequest r " .
			   		   "			       WHERE r.PlannedRelease = t.pm_VersionId" .
			   		   "				     AND r.State NOT IN ('".join("','",$states)."')))";
				
		    default:
			    
				return " AND 1 = 2 ";
 		}
 	}
}
