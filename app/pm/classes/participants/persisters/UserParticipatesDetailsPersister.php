<?php

class UserParticipatesDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $project_it = getSession()->getProjectIt();
 	    
 		$columns = array();

 		$columns[] =
     		"( SELECT GROUP_CONCAT(CAST(r.pm_ParticipantRoleId AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r, pm_Participant n " .
			" 	WHERE r.Participant = n.pm_ParticipantId ".
			"     AND r.Project = ".$project_it->getId().
			"     AND n.Project = r.Project ".
 		    "     AND n.SystemUser = ".$this->getPK($alias)." ) ParticipantRole ";

 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(t.pm_ParticipantId AS CHAR))" .
     		"  	 FROM pm_Participant t " .
			" 	WHERE t.Project = ".$project_it->getId().
			"     AND t.SystemUser = ".$this->getPK($alias).") Participant ";
 		
 		$columns[] =
     		"( SELECT GROUP_CONCAT(CAST(r.ProjectRole AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r, pm_Participant t " .
			" 	WHERE r.Participant = t.pm_ParticipantId ".
			"     AND t.Project = ".$project_it->getId().
			"     AND t.SystemUser = ".$this->getPK($alias).") ProjectRole ";
 			
 		$linked_ids = join(',', 
 				array_filter(
 						array_merge(
			 				preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject')),
			 				array( getSession()->getProjectIt()->getId() )
	 				)
 				)
 		);

        if ( defined('PERMISSIONS_ENABLED') ) {
            $columns[] =
                "( SELECT SUM(r.Capacity) " .
                "  	 FROM pm_ParticipantRole r, pm_Participant n " .
                " 	WHERE r.Participant = n.pm_ParticipantId " .
                "     AND n.Project IN (" . $linked_ids . ") ".
                "     AND n.SystemUser = " . $this->getPK($alias) . " ) Capacity ";
        }
        else {
            $columns[] = " (SELECT 8) Capacity ";
        }

        $columns[] =
            "( SELECT MAX(IFNULL(t.IsActive,'N')) " .
            "  	 FROM pm_Participant t " .
            " 	WHERE t.Project IN (" . $linked_ids . ") ".
            "     AND t.SystemUser = ".$this->getPK($alias).") IsActive ";

        $columns[] =
     		"( SELECT GROUP_CONCAT(CAST(t.Project AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r, pm_Participant t " .
			" 	WHERE r.Participant = t.pm_ParticipantId ".
			"     AND r.Project IN (".$linked_ids.") ".
 		    "     AND t.SystemUser = ".$this->getPK($alias).") Project ";

 		$columns[] = 
     		"IF( (SELECT COUNT(1)" .
     		"  	    FROM pm_Participant t " .
			" 	   WHERE t.Project = ".$project_it->getId().
			"        AND t.SystemUser = ".$this->getPK($alias).") > 0, 1, ".
			"     IF( (SELECT COUNT(1)" .
     		"  	         FROM pm_Participant t " .
			" 	        WHERE t.SystemUser = ".$this->getPK($alias).
			"			  AND t.Project IN (".$linked_ids.") ) > 0, 2, 3) ".
 		    ") ParticipanceType ";

 		if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
 		{
            $columns[] =
                "( SELECT SUM(t.LeftWork) " .
                "  	 FROM pm_Task t " .
                " 	WHERE t.FinishDate IS NULL ".
                "     AND t.Assignee = ".$this->getPK($alias).") LeftWork ";
        }

 		return $columns;
 	}
}
