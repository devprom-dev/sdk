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
		   " SELECT concat(LPAD(v.Caption,8,'0'), '.', LPAD(r.ReleaseNumber,8,'0')), " .
		   "		concat(v.Caption, '.', r.ReleaseNumber), " .
		   "		concat(v.Caption, '.', r.ReleaseNumber), " .
		   "		r.Version, " .
		   "		r.pm_ReleaseId, ".
		   "        '', " .
		   "		r.Description, ".
		   "		v.Project, v.VPD, r.StartDate, r.FinishDate, r.RecordCreated, r.RecordModified " .
		   "   FROM pm_Release r, pm_Version v ".
		   "  WHERE 1 = 1 ".$iteration->getVpdPredicate('r').
		   "    AND v.pm_VersionId = r.Version ";
 	    
		$sql .= 			   
		   "  UNION ".
		   " SELECT concat(LPAD(v.Caption,8,'0'), '.', LPAD(b.Caption,8,'0')), " .
		   "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''), concat(v.Caption, '.', b.Caption)), " .
		   "		concat(v.Caption, '.', b.Caption), " .
		   "		b.Version, " .
		   "		'', ".
		   "        b.pm_BuildId, " .
		   "		b.Description, ".
		   "		v.Project, b.VPD, v.StartDate, v.FinishDate, v.RecordCreated, v.RecordModified " .
		   "   FROM pm_Build b, pm_Version v ".
		   "  WHERE 1 = 1 ".$build->getVpdPredicate('b').
		   "    AND v.pm_VersionId = b.Version ";
		
		$sql .= 			   
		   "  UNION ".
		   " SELECT concat(LPAD(v.Caption,8,'0'), '.', LPAD(b.Caption,8,'0')), " .
		   "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''), concat(v.Caption, '.', b.Caption)), " .
		   "		concat(v.Caption, '.', b.Caption), " .
		   "		b.Version, " .
		   "		r.pm_ReleaseId, ".
		   "        b.pm_BuildId, " .
		   "		b.Description, ".
		   "		v.Project, b.VPD, r.StartDate, r.FinishDate, r.RecordCreated, r.RecordModified " .
		   "   FROM pm_Build b, pm_Release r, pm_Version v ".
		   "  WHERE 1 = 1 ".$build->getVpdPredicate('b').
		   "    AND r.pm_ReleaseId = b.Release ".
		   "    AND v.pm_VersionId = r.Version ";
		
		$sql .= 			   
		   "  UNION ".
		   " SELECT concat(LPAD('',8,'0'), '.', LPAD(b.Caption,8,'0')), " .
		   "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''),b.Caption), " .
		   "		b.Caption, " .
		   "		'', " .
		   "		'', ".
		   "        b.pm_BuildId, " .
		   "		b.Description, ".
		   "		(SELECT v.pm_ProjectId FROM pm_Project v WHERE v.VPD = b.VPD), ".
		   "        b.VPD, ".
		   "        NOW(), NOW(), b.RecordCreated, b.RecordModified " .
		   "   FROM pm_Build b ".
		   "  WHERE 1 = 1 ".$build->getVpdPredicate('b').
		   "    AND b.Release IS NULL ".
		   "    AND b.Version IS NULL ";
		
		return "(".$sql.")";
 	}	
}