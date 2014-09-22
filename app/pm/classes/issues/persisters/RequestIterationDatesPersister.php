<?php

class RequestIterationDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"(SELECT MIN(r.StartDate) FROM pm_Release r, pm_Task s ".
 			"  WHERE s.ChangeRequest = ".$this->getPK($alias).
 			"    AND s.Release = r.pm_ReleaseId ) IterationStartDate ";

 		$columns[] =  
 			"(SELECT MAX(r.FinishDate) FROM pm_Release r, pm_Task s ".
 			"  WHERE s.ChangeRequest = ".$this->getPK($alias).
 			"    AND s.Release = r.pm_ReleaseId ) IterationFinishDate ";
 		
 		$columns[] =  
 			"(SELECT MIN(r.MetricValueDate) FROM pm_IterationMetric r, pm_Task q ".
 			"  WHERE q.ChangeRequest = ".$this->getPK($alias).
 			"    AND q.Release = r.Iteration ".
 		    "    AND r.Metric = 'EstimatedStart' ) IterationEstimatedStart ";
 		
 		$columns[] =  
 			"(SELECT MAX(r.MetricValueDate) FROM pm_IterationMetric r, pm_Task q ".
 			"  WHERE q.ChangeRequest = ".$this->getPK($alias).
 			"    AND q.Release = r.Iteration ".
 		    "    AND r.Metric = 'EstimatedFinish' ) IterationEstimatedFinish ";
 		
 		return $columns;
 	}
}
