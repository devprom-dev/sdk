<?php

class RequestReleaseDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"IFNULL( ".
 			"	  (SELECT MIN(v.StartDate) FROM pm_Version v, pm_Release r, pm_Task q ".
 			"  	    WHERE v.pm_VersionId = r.Version ".
 			"  	 	  AND r.pm_ReleaseId = q.Release ".
 			"		  AND q.ChangeRequest = ".$this->getPK($alias).
 			"      ), ".
 			"	  (SELECT r.StartDate FROM pm_Version r WHERE r.pm_VersionId = t.PlannedRelease)".
 			") ReleaseStartDate ";

 		$columns[] =  
 			"IFNULL( ".
 			"	  (SELECT MAX(v.FinishDate) FROM pm_Version v, pm_Release r, pm_Task q ".
 			"  	    WHERE v.pm_VersionId = r.Version ".
 			"  	 	  AND r.pm_ReleaseId = q.Release ".
 			"		  AND q.ChangeRequest = ".$this->getPK($alias).
 			"      ), ".
 			"	  (SELECT r.FinishDate FROM pm_Version r WHERE r.pm_VersionId = t.PlannedRelease)".
 			") ReleaseFinishDate ";

 		$columns[] =  
 			"IFNULL( ".
 			"	  (SELECT MAX(v.MetricValueDate) FROM pm_VersionMetric v, pm_Release r, pm_Task q ".
 			"  	    WHERE v.Version = r.Version ".
 			"		  AND v.Metric = 'EstimatedStart' ".
 			"  	 	  AND r.pm_ReleaseId = q.Release ".
 			"		  AND q.ChangeRequest = ".$this->getPK($alias).
 			"      ), ".
 			"	  (SELECT MAX(r.MetricValueDate) FROM pm_VersionMetric r ".
 			"		WHERE r.Version = t.PlannedRelease AND r.Metric = 'EstimatedStart')".
 			") ReleaseEstimatedStart ";
 		
 		$columns[] =  
 			"IFNULL( ".
 			"	  (SELECT MAX(v.MetricValueDate) FROM pm_VersionMetric v, pm_Release r, pm_Task q ".
 			"  	    WHERE v.Version = r.Version ".
 			"		  AND v.Metric = 'EstimatedFinish' ".
 			"  	 	  AND r.pm_ReleaseId = q.Release ".
 			"		  AND q.ChangeRequest = ".$this->getPK($alias).
 			"      ), ".
 			"	  (SELECT MAX(r.MetricValueDate) FROM pm_VersionMetric r ".
 			"		WHERE r.Version = t.PlannedRelease AND r.Metric = 'EstimatedFinish')".
 			") ReleaseEstimatedFinish ";
 		
 		return $columns;
 	}
}
