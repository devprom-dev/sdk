<?php

class ProjectLeadsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    
		$sql = "(SELECT GROUP_CONCAT(CAST(p.SystemUser AS CHAR)) FROM pm_Participant p " .
			   "     WHERE EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole l" .
			   "				    WHERE r.Participant = p.pm_ParticipantId " .
			   "                      AND r.ProjectRole = l.pm_ProjectRoleId" .
			   "				      AND l.ReferenceName = 'lead' ) " .
			   "           AND p.IsActive = 'Y' ".
			   "           AND p.Project = ".$this->getPK($alias).") Coordinators ";
 	    
 		$columns[] = $sql;

 		return $columns;
 	}
}
