<?php

class ActivityEx2TimingPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('TaskType');
    }

    function getSelectColumns( $alias )
    {
         return array (
             " (SELECT s.TaskType FROM pm_Task s WHERE s.pm_TaskId = ".$alias.".Task) TaskType "
         );
    }

    function modify( $object_id, $parms )
    {
        if ( $parms['TaskType'] != '' && $parms['Task'] != '' ) {
            getFactory()->getObject('Task')->modify_parms($parms['Task'], array(
                'TaskType' => $parms['TaskType']
            ));
        }
    }
}