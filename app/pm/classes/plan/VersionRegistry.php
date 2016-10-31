<?php

class VersionRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 		$release = getFactory()->getObject('Release');
 		$iteration = getFactory()->getObject('Iteration');
 		$build = getFactory()->getObject('Build');
 		
 		$sql = " SELECT LPAD(v.Caption, 8, '0') VersionNumber, " .
 			   "		v.Caption Caption, ".
 			   "		v.Caption pm_VersionId, ".
 			   "		v.pm_VersionId Version, " .
 			   "	    '' `Release`, " .
 			   "		'' Build, " .
 			   "		v.Description, v.Project, v.VPD, v.StartDate, v.FinishDate, v.RecordCreated, v.RecordModified" .
 			   "   FROM pm_Version v ".
 			   "  WHERE 1 = 1 ".$release->getVpdPredicate('v');
 		
		$sql .= 			   
		   "  UNION ".
		   " SELECT IF(v.Caption IS NULL, LPAD(r.ReleaseNumber, 8, '0'), concat(LPAD(v.Caption,8,'0'), '.', LPAD(r.ReleaseNumber,8,'0'))), " .
		   "		IF(v.Caption IS NULL, r.ReleaseNumber, concat(v.Caption, '.', r.ReleaseNumber)), " .
		   "		IF(v.Caption IS NULL, r.ReleaseNumber, concat(v.Caption, '.', r.ReleaseNumber)), " .
		   "		r.Version, " .
		   "		r.pm_ReleaseId, ".
		   "        '', " .
		   "		r.Description, ".
		   "		(SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = r.VPD) Project, ".
		   "		r.VPD, r.StartDate, r.FinishDate, r.RecordCreated, r.RecordModified " .
		   "   FROM pm_Release r LEFT OUTER JOIN pm_Version v ON r.Version = v.pm_VersionId".
		   "  WHERE 1 = 1 ".$iteration->getVpdPredicate('r');
 	    
		$sql .=
		   "  UNION ".
		   " SELECT concat(LPAD('',8,'0'), '.', LPAD(b.Caption,8,'0')), " .
		   "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''),b.Caption), " .
		   "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''),b.Caption), " .
		   "		'', " .
		   "		'', ".
		   "        b.pm_BuildId, " .
		   "		b.Description, ".
		   "		(SELECT v.pm_ProjectId FROM pm_Project v WHERE v.VPD = b.VPD), ".
		   "        b.VPD, ".
		   "        NOW(), NOW(), b.RecordCreated, b.RecordModified " .
		   "   FROM pm_Build b ".
		   "  WHERE 1 = 1 ".$build->getVpdPredicate('b');
		
		return "(".$sql.")";
 	}	
}