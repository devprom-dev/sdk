<?php

class IterationArtefactsPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array (
			'Issues', 'Tasks', 'Increments'
		);
	}

 	function getSelectColumns( $alias )
 	{
 	    $columns = array(
            "(SELECT GROUP_CONCAT(CAST(s2.pm_TaskId AS CHAR)) FROM pm_Task s2 WHERE ".$alias.".pm_ReleaseId = s2.Release) Tasks "
        );

 	    if (getSession()->IsRDD()) {
            $columns[] =
                " (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a 
			          WHERE a.Iteration = ".$alias.".pm_ReleaseId AND a.Type IS NULL) Issues ";
            $columns[] =
                " (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) FROM pm_ChangeRequest a 
			          WHERE a.Iteration = ".$alias.".pm_ReleaseId AND a.Type IS NOT NULL ) Increments  ";
        }
 	    else {
            $columns[] =
                " (SELECT GROUP_CONCAT(CAST(a.pm_ChangeRequestId AS CHAR)) 
                     FROM pm_ChangeRequest a WHERE a.Iteration = ".$alias.".pm_ReleaseId ) Issues ";
        }
		return $columns;
 	}
}
