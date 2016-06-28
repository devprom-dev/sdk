<?php

class ReleaseArtefactsPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array (
			'Issues', 'Tasks'
		);
	}

 	function getSelectColumns( $alias )
 	{
		return array (
			"(SELECT GROUP_CONCAT(CAST(s.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest s WHERE s.PlannedRelease = ".$alias.".pm_VersionId ) Issues ",
			"(SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s, pm_Release r WHERE r.Version = ".$alias.".pm_VersionId AND r.pm_ReleaseId = s.Release ) Tasks"
		);
 	}
}
