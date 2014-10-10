<?php

class UserParticipatesDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    global $model_factory;
 	    
 	    $project_it = getSession()->getProjectIt();
 	    
 		$columns = array();

 		$columns[] =  
 			"( SELECT SUM(r.Capacity) " .
			"  	 FROM pm_ParticipantRole r, pm_Participant n " .
			" 	WHERE r.Participant = n.pm_ParticipantId ".
			"     AND n.Project = ".$project_it->getId().
 		    "     AND n.SystemUser = ".$this->getPK($alias)." ) Capacity ";
 		
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
     		"( SELECT IFNULL(GROUP_CONCAT(t.IsActive),'N') " .
     		"  	 FROM pm_Participant t " .
			" 	WHERE t.Project = ".$project_it->getId().
			"     AND t.SystemUser = ".$this->getPK($alias).") IsActive ";
 		
 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(r.ProjectRole AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r, pm_Participant t " .
			" 	WHERE r.Participant = t.pm_ParticipantId ".
			"     AND t.Project = ".$project_it->getId().
			"     AND t.SystemUser = ".$this->getPK($alias).") ProjectRole ";
 			
 		$participant = $model_factory->getObject('Participant');
 		
 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(t.Project AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r, pm_Participant t " .
			" 	WHERE r.Participant = t.pm_ParticipantId ".$participant->getVpdPredicate().
 		    "     AND t.SystemUser = ".$this->getPK($alias).") Project ";

 		$columns[] = 
     		"IF( (SELECT COUNT(1)" .
     		"  	    FROM pm_Participant t " .
			" 	   WHERE t.Project = ".$project_it->getId().
			"        AND t.SystemUser = ".$this->getPK($alias).") > 0, 1, ".
			"     IF( (SELECT COUNT(1)" .
     		"  	         FROM pm_Participant t " .
			" 	        WHERE t.SystemUser = ".$this->getPK($alias).$participant->getVpdPredicate().") > 0, 2, 3) ".
 		    ") ParticipanceType ";
 		
 		return $columns;
 	}
}
