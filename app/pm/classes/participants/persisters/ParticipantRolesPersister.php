<?php

class ParticipantRolesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

		$columns[] =
			"( SELECT r.IsReadonly FROM cms_User r WHERE r.cms_UserId = ".$alias.".SystemUser ) IsReadonly ";

 		$columns[] =
     		"( SELECT GROUP_CONCAT(CAST(r.pm_ParticipantRoleId AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r " .
     		" 	WHERE r.Participant = ".$this->getPK($alias)." ) ParticipantRole ";

 		$columns[] = 
     		"( SELECT GROUP_CONCAT(CAST(r.ProjectRole AS CHAR))" .
     		"  	 FROM pm_ParticipantRole r " .
     		" 	WHERE r.Participant = ".$this->getPK($alias).") ProjectRole ";
 			
 		$columns[] = 
     		"( SELECT GROUP_CONCAT(pr.ReferenceName)" .
     		"  	 FROM pm_ParticipantRole r, pm_ProjectRole pr " .
     		" 	WHERE r.Participant = ".$this->getPK($alias).
 		    "     AND pr.pm_ProjectRoleId = r.ProjectRole ) ProjectRoleReferenceName ";
 		
 		return $columns;
 	}
}
