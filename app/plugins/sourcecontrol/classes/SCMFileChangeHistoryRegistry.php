<?php

class SCMFileChangeHistoryRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		return " (SELECT t.pm_ScmFileChangesId,
						 t.Repository,
		 				 t.Author,
		 				 t.Revision,
		 				 t.FilePath,
		 				 t.Modified,
		 				 t.Deleted,
		 				 t.Inserted,
		 				 IFNULL(t.VPD, '".$this->getObject()->getVpdValue()."') VPD,
		 				 i.StartDate RecordCreated,
		 				 i.StartDate RecordModified,
		 				 UNIX_TIMESTAMP(i.StartDateOnly) DayDate,
		 				 (SELECT IFNULL(t.Modified / SUM(a.Capacity), 0)
		 				    FROM pm_Activity a, pm_SubversionUser u
		 				   WHERE a.Participant = u.SystemUser
		 				     AND a.VPD = t.VPD
		 				     AND u.Connector = t.Repository
		 				     AND u.UserName = t.Author
		 				     AND a.ReportDate = i.StartDateOnly) ModifiedPerHour
					FROM pm_CalendarInterval i LEFT OUTER JOIN pm_ScmFileChanges t ON i.StartDateOnly = DATE(t.RecordCreated)
				   WHERE i.Kind = 'day' AND i.StartDate <= NOW()
				  ) ";
	}
}