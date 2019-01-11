<?php

class StageRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    $release = getFactory()->getObject('Release');
 	    $projectPrefix = getSession()->getProjectIt()->get('LinkedProject') != '';

 		$sql = " SELECT LPAD(v.Caption, 8, '0') VersionNumber, " .
 			   "		v.Caption Caption, ".
               ($projectPrefix ? "CONCAT('{',(SELECT p.Caption FROM pm_Project p WHERE p.VPD = v.VPD),'} ')" : "''"). " CaptionPrefix, ".
               "		CONCAT('".translate('Релиз')." ', v.Caption) CaptionType, ".
 			   "		v.pm_VersionId pm_VersionId, ".
 			   "		v.pm_VersionId Version, " .
 			   "	    '' `Release`, " .
 			   "		'' Build, " .
 			   "		DATE(v.EstimatedStartDate) EstimatedStartDate, " .
 			   "		DATE(v.EstimatedFinishDate) EstimatedFinishDate, " .
 			   "		DATE(v.StartDate) ActualStartDate, " .
 			   "		DATE(v.FinishDate) ActualFinishDate, " .
                "		DATE(v.StartDate) Deadlines, " .
 				"		'' IterationDate," .
                "       '' RecentComment,".
 			   "		v.Description, ".
 			   "        v.Project, ".
 			   "        v.VPD, ".
 			   "        v.IsActual, ".
 			   "        DATE(v.StartDate) StartDate, ".
 			   "        DATE(v.FinishDate) FinishDate, " .
			   "        (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s " .
			   "	      WHERE s.PlannedRelease = v.pm_VersionId AND s.Iteration IS NULL ) Issues, ".
               "        (SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s " .
               "	      WHERE s.PlannedRelease = v.pm_VersionId) Increments, ".
			   "        (SELECT COUNT(1) FROM pm_ChangeRequest s " .
			   "	      WHERE s.PlannedRelease = v.pm_VersionId" .
			   "			AND s.FinishDate IS NULL ) UncompletedIssues, ".
			   "        (SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s, pm_ChangeRequest r " .
			   "	      WHERE r.PlannedRelease = v.pm_VersionId AND r.Iteration IS NULL " .
               "            AND r.pm_ChangeRequestId = s.ChangeRequest ".
			   "            AND s.Release IS NULL ) Tasks, ".
			   "         0 UncompletedTasks, ".
     		   "        (SELECT GROUP_CONCAT(CAST(m.pm_ReleaseId AS CHAR)) " .
    		   "           FROM pm_Release m" .
    		   "          WHERE m.Version = v.pm_VersionId) Iterations, ".
               "		v.RecordCreated, ".
    		   "		v.RecordModified, ".
    		   "		v.RecordVersion, ".
               "        CONCAT(LPAD(v.pm_VersionId, 8, '0'),LPAD('',8,'0')) Stage, ".
               "        'Release' State ".
    		   "   FROM (SELECT v.*," .
 			   "		        IFNULL((SELECT MIN(m.MetricValueDate) FROM pm_VersionMetric m " .
 			   "		  			     WHERE m.Version = v.pm_VersionId " .
 			   "						   AND m.Metric = 'EstimatedStart'), v.StartDate) EstimatedStartDate, " .
 			   "		        IFNULL((SELECT MAX(m.MetricValueDate) FROM pm_VersionMetric m " .
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
           ($projectPrefix ? "CONCAT('{',(SELECT p.Caption FROM pm_Project p WHERE p.VPD = r.VPD),'} ')" : "''"). " CaptionPrefix, ".
           "		IF(v.Caption IS NULL, CONCAT('".translate('Итерация')." ',r.ReleaseNumber), CONCAT('".translate('Итерация')." ',v.Caption,'.',r.ReleaseNumber)), ".
		   "		r.pm_ReleaseId, " .
		   "		r.Version, " .
		   "		r.pm_ReleaseId, '', " .
		   "		DATE(r.EstimatedStartDate), " .
		   "		DATE(r.EstimatedFinishDate), " .
 		   "		DATE(r.StartDate) ActualStartDate, " .
 		   "		DATE(r.FinishDate) ActualFinishDate, " .
           "		DATE(r.StartDate) Deadlines, " .
		   "		DATE(r.StartDate) IterationDate," .
           "        '' RecentComment,".
		   "		r.Description, ".
		   "		r.Project, ".
		   "        r.VPD, ".
		   "        r.IsActual, ".
		   "        DATE(r.StartDate), ".
		   "        DATE(r.FinishDate), " .
		   "       (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a WHERE a.Iteration = r.pm_ReleaseId) Issues, ".
           "       (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a 
                          WHERE a.Iteration = r.pm_ReleaseId) Increments, ".
		   "         0 UncompletedIssues, ".
		   "        (SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
		   "	      WHERE r.pm_ReleaseId = s.Release ) Tasks, ".
		   "        (SELECT COUNT(1) FROM pm_Task s " .
		   "	      WHERE r.pm_ReleaseId = s.Release ".
		   "			AND s.FinishDate IS NULL) UncompletedTasks, ".
 		   "        r.pm_ReleaseId Iterations, ".
    	   "		r.RecordCreated, ".
    	   "		r.RecordModified, ".
    	   "		r.RecordVersion, ".
           "        CONCAT(LPAD(IFNULL(v.pm_VersionId, 0), 8, '0'),LPAD(r.pm_ReleaseId, 8, '0')) Stage, ".
           "        'Iteration' State ".
    	   "   FROM (SELECT r.*, ".
 		   "		        IFNULL((SELECT MIN(m.MetricValueDate) FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedStart'), r.StartDate) EstimatedStartDate, " .
 		   "		        IFNULL((SELECT MAX(m.MetricValueDate) FROM pm_IterationMetric m " .
 		   "		  			     WHERE m.Iteration = r.pm_ReleaseId " .
 		   "						   AND m.Metric = 'EstimatedFinish'), r.FinishDate) EstimatedFinishDate" .
 		   "           FROM pm_Release r ".
		   "		  WHERE 1 = 1 ".$iteration->getVpdPredicate('r').") r ".
		   "		   	LEFT OUTER JOIN pm_Version v ON v.pm_VersionId = r.Version ";
 	    
 	    return "(".$sql.")";
 	}
}