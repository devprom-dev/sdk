<?php

class RequestTasksDetailPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array(
            'TasksPlanned', 'TasksLeft'
        );
    }

    function getSelectColumns( $alias )
 	{
 		return array(
            "(SELECT SUM(s.Planned) FROM pm_Task s 
               WHERE s.ChangeRequest = {$this->getPK($alias)} ) TasksPlanned ",

            "(SELECT SUM(s.LeftWork) FROM pm_Task s 
               WHERE s.ChangeRequest = {$this->getPK($alias)} ) TasksLeft "
        );
 	}
}
