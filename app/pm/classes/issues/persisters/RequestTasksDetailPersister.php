<?php

class RequestTasksDetailPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array(
            'TasksPlanned'
        );
    }

    function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =
 	 		"(SELECT SUM(s.Planned) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) TasksPlanned ";

 		return $columns;
 	}
}
