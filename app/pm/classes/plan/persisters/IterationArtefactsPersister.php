<?php

class IterationArtefactsPersister extends ObjectSQLPersister
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
			" CONCAT_WS(',',
			    (SELECT GROUP_CONCAT(CAST(a.ChangeRequest AS CHAR)) FROM pm_Task a WHERE a.Release = ".$alias.".pm_ReleaseId),
			    (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a WHERE a.Iteration = ".$alias.".pm_ReleaseId)
			  ) Issues ",
			"(SELECT GROUP_CONCAT(CAST(s2.pm_TaskId AS CHAR)) FROM pm_Task s2 WHERE ".$alias.".pm_ReleaseId = s2.Release) Tasks "
		);
 	}
}
