<?php

class RequestTasksPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array('Tasks', 'OpenTasks');
    }

    function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =
 	 		"(SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) Tasks ";

 		$columns[] =
 	 		"(SELECT GROUP_CONCAT(CAST(s.pm_TaskId AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias).
 			"	 AND s.FinishDate IS NULL ) OpenTasks ";
 		
 		return $columns;
 	}
}
