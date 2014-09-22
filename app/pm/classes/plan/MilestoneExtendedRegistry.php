<?php

class MilestoneExtendedRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
		$sql = "SELECT t.pm_MilestoneId, t.MilestoneDate, t.Caption, t.Description, t.RecordVersion, t.RecordModified, t.RecordCreated, " .
			   "	   IFNULL(t.Passed, 'N') Passed, t.ReasonToChangeDate, t.CompleteResult, 'pm_Milestone' ObjectClass, t.VPD, t.pm_MilestoneId ObjectId ".
			   "  FROM pm_Milestone t ";
			   
		$sql .=
		   " UNION ".
		   "SELECT NULL, t.FinishDate, 'ReleaseFinish', '', NULL, NULL, NULL, " .
		   "	   IF(DATE(t.FinishDate) < DATE(NOW()),'Y','N'), '', '', 'pm_Version', t.VPD, t.pm_VersionId ObjectId ".
		   "  FROM pm_Version t ".
		   " UNION ".
		   "SELECT NULL, t.StartDate, 'ReleaseStart', '', NULL, NULL, NULL, " .
		   "	   IF(DATE(t.StartDate) < DATE(NOW()),'Y','N'), '', '', 'pm_Version', t.VPD, t.pm_VersionId ObjectId ".
		   "  FROM pm_Version t ";

		$sql .=
		   " UNION ".
		   "SELECT NULL, t.FinishDate, 'IterationFinish', '', NULL, NULL, NULL, " .
		   "	   IF(DATE(t.FinishDate) < DATE(NOW()),'Y','N'), '', '', 'pm_Release', t.VPD, t.pm_ReleaseId ObjectId ".
		   "  FROM pm_Release t ".
		   " UNION ".
		   "SELECT NULL, t.StartDate, 'IterationStart', '', NULL, NULL, NULL, " .
		   "	   IF(DATE(t.StartDate) < DATE(NOW()),'Y','N'), '', '', 'pm_Release', t.VPD, t.pm_ReleaseId ObjectId ".
		   "  FROM pm_Release t "; 	    
 	    
		return "( ".$sql." )";
 	}
}