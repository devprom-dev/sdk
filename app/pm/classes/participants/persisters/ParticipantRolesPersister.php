<?php

class ParticipantRolesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $project_it = getSession()->getProjectIt();
 	    
 		$columns = array();

 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(r.pm_ParticipantRoleId AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r " .
     		" 	WHERE r.Participant = ".$this->getPK($alias)." ) ParticipantRole ";

 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(r.ProjectRole AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r " .
     		" 	WHERE r.Participant = ".$this->getPK($alias).
 		    "     AND r.Project = ".($project_it->getId() > 0 ? $project_it->getId(): ' r.Project ').") ProjectRole ";
 			
 		$columns[] = 
     		"( SELECT GROUP_CONCAT(pr.ReferenceName)" .
     		"  	 FROM pm_ParticipantRole r, pm_ProjectRole pr " .
     		" 	WHERE r.Participant = ".$this->getPK($alias).
 		    "     AND pr.pm_ProjectRoleId = r.ProjectRole ) ProjectRoleReferenceName ";
 		
 		return $columns;
 	}
}
