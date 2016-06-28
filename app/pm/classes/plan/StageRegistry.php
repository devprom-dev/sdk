<?php

class StageRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    $release = getFactory()->getObject('Release');
		$states = getFactory()->getObject('pm_ChangeRequest')->getNonTerminalStates();
		$task_states = getFactory()->getObject('Task')->getNonTerminalStates();

 		$sql = " SELECT LPAD(v.Caption, 8, '0') VersionNumber, " .
 			   "		v.Caption Caption, ".
               "		CONCAT('".translate('Релиз')." ', v.Caption) CaptionType, ".
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
			   "        (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s " .
			   "	      WHERE s.PlannedRelease = v.pm_VersionId ) Issues, ".
			   "        (SELECT COUNT(1) FROM pm_ChangeRequest s " .
			   "	      WHERE s.PlannedRelease = v.pm_VersionId" .
			   "			AND s.State IN ('".join("','",$states)."')) UncompletedIssues, ".
			   "        (SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s, pm_Release r " .
			   "	      WHERE r.Version = v.pm_VersionId" .
			   "            AND r.pm_ReleaseId = s.Release ) Tasks, ".
			   "         0 UncompletedTasks, ".
     		   "        (SELECT GROUP_CONCAT(CAST(m.pm_ReleaseId AS CHAR)) " .
    		   "           FROM pm_Release m" .
    		   "          WHERE m.Version = v.pm_VersionId) Iterations, ".
               "		v.RecordCreated, ".
    		   "		v.RecordModified, ".
    		   "		UNIX_TIMESTAMP(v.RecordModified) * 100000 AffectedDate ".
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
 		
 		$iteration = getFactory()->getObject('Iteration');

		$sql .= 			   
		   "  UNION ".
		   " SELECT IF(v.Caption IS NULL, LPAD(r.ReleaseNumber,8,'0'), concat(LPAD(v.Caption,8,'0'), '.', LPAD(r.ReleaseNumber,8,'0'))), " .
		   "		IF(v.Caption IS NULL, r.ReleaseNumber, concat(v.Caption, '.', r.ReleaseNumber)), " .
           "		IF(v.Caption IS NULL, CONCAT('".translate('Итерация')." ',r.ReleaseNumber), CONCAT('".translate('Итерация')." ',v.Caption,'.',r.ReleaseNumber)), ".
		   "		r.pm_ReleaseId, " .
		   "		r.Version, " .
		   "		r.pm_ReleaseId, '', " .
		   "		r.EstimatedStartDate, " .
		   "		r.EstimatedFinishDate, " .
 		   "		r.StartDate ActualStartDate, " .
 		   "		r.FinishDate ActualFinishDate, " .
		   "		r.StartDate IterationDate," .
		   "		r.Description, ".
		   "		r.Project, ".
		   "        r.VPD, ".
		   "        r.IsActual, ".
		   "        r.StartDate, ".
		   "        r.FinishDate, " .
		   "        (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s, pm_Task k " .
		   "	      WHERE k.ChangeRequest = s.pm_ChangeRequestId " .
		   "            AND k.Release = r.pm_ReleaseId ) Issues, ".
		   "         0 UncompletedIssues, ".
		   "        (SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
		   "	      WHERE r.pm_ReleaseId = s.Release ) Tasks, ".
		   "        (SELECT COUNT(1) FROM pm_Task s " .
		   "	      WHERE r.pm_ReleaseId = s.Release ".
		   "			AND s.State IN ('".join("','",$task_states)."')) UncompletedTasks, ".
 		   "        r.pm_ReleaseId Iterations, ".
    	   "		r.RecordCreated, ".
    	   "		r.RecordModified, ".
    	   "		UNIX_TIMESTAMP(v.RecordModified) * 100000 AffectedDate ".
    	   "   FROM (SELECT r.*, ".
 		   "		        IFNULL((SELECT m.MetricValueDate FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedStart'), r.StartDate) EstimatedStartDate, " .
 		   "		        IFNULL((SELECT m.MetricValueDate FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedFinish'), r.FinishDate) EstimatedFinishDate" .
 		   "           FROM pm_Release r ".
		   "		  WHERE 1 = 1 ".$iteration->getVpdPredicate('r').") r ".
		   "		   	LEFT OUTER JOIN pm_Version v ON v.pm_VersionId = r.Version ";
 	    
 	    return "(".$sql.")";
 	}
}