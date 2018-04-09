<?php

class RequestIterationsPersister extends ObjectSQLPersister
{
	function map( & $parms )
	{
		if ( !array_key_exists('Iteration',$parms) ) return;

        if ( $parms['Iteration'] != '' ) {
            $iteration_it = $this->getObject()->getAttributeObject('Iteration')->getExact(preg_split('/,/', $parms['Iteration']));
            $parms['PlannedRelease'] = $iteration_it->get('Version');
        }
	}

    function add($object_id, $parms)
    {
        if ( array_key_exists('Iteration', $parms) ) {
            $this->updateRelatedTasks($object_id, $parms);
        }
    }

    function modify( $object_id, $parms )
 	{
		if ( array_key_exists('Iteration', $parms) ) {
            $this->updateRelatedTasks($object_id, $parms);
        }
 	}

    protected function updateRelatedTasks( $object_id, $parms )
    {
        $object_it = $this->getObject()->getExact($object_id);

        $iteration_ids = array_filter(preg_split('/,/', $parms['Iteration']), function($value) {
            return is_numeric($value) && $value > 0;
        });
        if( count($iteration_ids) < 1 ) $iteration_ids[] = 'NULL';

        $task = getFactory()->getObject('Task');
        $task->removeNotificator( 'EmailNotificator' );

        if( $object_it->object->getAttributeType('OpenTasks') != '' ) {
            $task_it = $object_it->getRef('OpenTasks');
        }
        else {
            $task_it = $task->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ChangeRequest', $object_id)
                )
            );
        }

        while ( !$task_it->end() )
        {
            $task_it->object->modify_parms(
                $task_it->getId(),
                array('Release' => $iteration_ids[0])
            );
            $task_it->moveNext();
        }
    }
}
