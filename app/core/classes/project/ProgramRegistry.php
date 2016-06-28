<?php

class ProgramRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		$user_id = getSession()->getUserIt()->getId();
		
		return " (SELECT t.*, ".
			   "		 (SELECT COUNT(1) FROM pm_Participant p ".
			   "		   WHERE p.Project = t.pm_ProjectId ".
			   "			 AND p.SystemUser = ".($user_id > 0 ? $user_id : 0).") IsParticipant ".
			   "    FROM pm_Project t WHERE t.IsTender = 'Y') ";
	}
}