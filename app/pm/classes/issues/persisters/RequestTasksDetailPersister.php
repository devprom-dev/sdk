<?php

class RequestTasksDetailPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array(
            'TasksPlanned'
        );
    }

    function getSelectColumns( $alias )
 	{
 		return array(
            "(SELECT SUM(s.Planned) FROM pm_Task s " .
            "  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) TasksPlanned "
        );
 	}
}
