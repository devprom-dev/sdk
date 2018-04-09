<?php

class ParticipantRoleNamesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
     		"( SELECT GROUP_CONCAT(pr.Caption, ', ')" .
     		"  	 FROM pm_ParticipantRole r, pm_ProjectRole pr " .
     		" 	WHERE r.Participant = ".$this->getPK($alias).
 		    "     AND pr.pm_ProjectRoleId = r.ProjectRole ) ProjectRoleName "
        );
 	}
}
