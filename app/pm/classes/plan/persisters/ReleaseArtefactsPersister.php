<?php

class ReleaseArtefactsPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array (
			'Issues'
		);
	}

 	function getSelectColumns( $alias ) {
		return array (
			"(SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) 
			    FROM pm_ChangeRequest s 
			   WHERE s.PlannedRelease = ".$alias.".pm_VersionId ) Issues "
		);
 	}
}
