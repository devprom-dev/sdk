<?php

class StageRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    global $model_factory;
 	    
 	    $release = $model_factory->getObject('Release');
 	    
		$issue = $model_factory->getObject('pm_ChangeRequest');
		
		$states = $issue->getNonTerminalStates();
 	    
 		$sql = " SELECT LPAD(v.Caption, 8, '0') VersionNumber, " .
 			   "		v.Caption Caption, ".
 			   "		v.pm_VersionId pm_VersionId, ".
 			   "		v.pm_VersionId Version, " .
 			   "	    '' `Release`, " .
 			   "		'' Build, " .
 			   "		v.EstimatedStartDate, " .
 			   "		v.EstimatedFinishDate, " .
 			   "		v.StartDate ActualStartDate, " .
 			   "		v.FinishDate ActualFinishDate, " .
 				"		'' IterationDate," .
 			   "		v.Description, ".
 			   "        v.Project, ".
 			   "        v.VPD, ".
 			   "        v.IsActual, ".
 			   "        v.StartDate, ".
 			   "        v.FinishDate, " .
		       "        (SELECT COUNT(1) FROM pm_ChangeRequest s " .
		       "	      WHERE s.PlannedRelease = v.pm_VersionId" .
		       "			AND s.State IN ('".join("','",$states)."')) UncompletedItems, ".
     		   "        (SELECT GROUP_CONCAT(CAST(m.pm_ReleaseId AS CHAR)) " .
    		   "           FROM pm_Release m" .
    		   "          WHERE m.Version = v.pm_VersionId) Iterations, ".
    		   "		v.RecordCreated, ".
    		   "		v.RecordModified ".
    		   "   FROM (SELECT v.*," .
 			   "		        IFNULL((SELECT m.MetricValueDate FROM pm_VersionMetric m " .
 			   "		  			     WHERE m.Version = v.pm_VersionId " .
 			   "						   AND m.Metric = 'EstimatedStart'), v.StartDate) EstimatedStartDate, " .
 			   "		        IFNULL((SELECT m.MetricValueDate FROM pm_VersionMetric m " .
 			   "		  			     WHERE m.Version = v.pm_VersionId " .
 			   "						   AND m.Metric = 'EstimatedFinish'), v.FinishDate) EstimatedFinishDate" .
 			   "           FROM pm_Version v WHERE 1 = 1 ".$release->getVpdPredicate('v').") v ".
 			   "  WHERE 1 = 1 ";
 		
 		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() ) return "(".$sql.")";
 		
 		$iteration = $model_factory->getObject('Iteration');
 		
	    $task = $model_factory->getObject('pm_Task');
		
		$states = $task->getNonTerminalStates();
 		
		$sql .= 			   
		   "  UNION ".
		   " SELECT concat(LPAD(v.Caption,8,'0'), '.', LPAD(r.ReleaseNumber,8,'0')), " .
		   "		concat(v.Caption, '.', r.ReleaseNumber), " .
		   "		r.pm_ReleaseId, " .
		   "		r.Version, " .
		   "		r.pm_ReleaseId, '', " .
		   "		r.EstimatedStartDate, " .
		   "		r.EstimatedFinishDate, " .
 		   "		r.StartDate ActualStartDate, " .
 		   "		r.FinishDate ActualFinishDate, " .
		   "		r.StartDate IterationDate," .
		   "		r.Description, ".
		   "		v.Project, ".
		   "        v.VPD, ".
		   "        r.IsActual, ".
		   "        r.StartDate, ".
		   "        r.FinishDate, " .
		   "        (SELECT COUNT(1) FROM pm_Task s " .
		   "	      WHERE s.Release = r.pm_ReleaseId" .
		   "			AND s.State IN ('".join("','",$states)."')) UncompletedItems, ".
 		   "        r.pm_ReleaseId Iterations, ".
    	   "		r.RecordCreated, ".
    	   "		r.RecordModified ".
 		   "   FROM pm_Version v, ".
		   "        (SELECT r.*, ".
 		   "		        IFNULL((SELECT m.MetricValueDate FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedStart'), r.StartDate) EstimatedStartDate, " .
 		   "		        IFNULL((SELECT m.MetricValueDate FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedFinish'), r.FinishDate) EstimatedFinishDate" .
 		   "           FROM pm_Release r ".
		   "		  WHERE 1 = 1 ".$iteration->getVpdPredicate('r').") r ".
		   "  WHERE v.pm_VersionId = r.Version ";
 	    
 	    return "(".$sql.")";
 	}
}