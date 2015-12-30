<?php

class RequestIterationsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			"(SELECT GROUP_CONCAT(DISTINCT CAST(s.Release AS CHAR)) FROM pm_Task s " .
			"  WHERE s.ChangeRequest = ".$this->getPK($alias)." ) Iterations ";

 		return $columns;
 	}

	function map( & $parms )
	{
		if ( !array_key_exists('Iterations',$parms) ) return;

        if ( $parms['Iterations'] == '' ) {
            $parms['PlannedRelease'] = '';
        }
        else {
            $iteration_it = $this->getObject()->getAttributeObject('Iterations')->getExact(preg_split('/,/', $parms['Iterations']));
            $parms['PlannedRelease'] = $iteration_it->get('Version');
        }
	}

    function add($object_id, $parms)
    {
        if ( array_key_exists('Iterations', $parms) ) {
            $this->updateRelatedTasks($object_id, $parms);
        }
    }

    function modify( $object_id, $parms )
 	{
		if ( array_key_exists('Iterations', $parms) ) {
            $this->updateRelatedTasks($object_id, $parms);
        }

        if ( array_key_exists('Estimation', $parms) && $this->getObject()->getAttributeType('OpenTasks') == '' ) {
            $this->updateTaskEsimtation($object_id, $parms);
        }
 	}

    protected function updateTaskEsimtation( $object_id, $parms )
    {
        $task = getFactory()->getObject('Task');
        $task->removeNotificator( 'EmailNotificator' );

        $task_it = $task->getRegistry()->Query(
            array (
                new FilterAttributePredicate('ChangeRequest', $object_id)
            )
        );
        if ( $task_it->getId() == '' ) return;

        $task->modify_parms($task_it->getId(), array (
            'Planned' => $parms['Estimation'],
            'LeftWork' => $parms['Estimation']
        ));
    }

    protected function updateRelatedTasks( $object_id, $parms )
    {
        $object_it = $this->getObject()->getExact($object_id);
        if ( $object_it->get('Iterations') == $parms['Iterations'] ) return;

        $iteration_ids = array_filter(preg_split('/,/', $parms['Iterations']), function($value) {
            return is_numeric($value) && $value > 0;
        });
        if( count($iteration_ids) < 1 ) $iteration_ids[] = 'NULL';

        $task = getFactory()->getObject('Task');
        $task->removeNotificator( 'EmailNotificator' );

        if( $this->getObject()->getAttributeType('OpenTasks') != '' ) {
            $task_it = $object_it->getRef('OpenTasks');
            if ( $task_it->count() < 1 ) {
                $task_it = $object_it->getRef('Tasks');
            }
        }
        else {
            $task_it = $task->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ChangeRequest', $object_id)
                )
            );
        }

        if ( $task_it->count() > 0 )
        {
            while ( !$task_it->end() )
            {
                $task_it->object->modify_parms(
                    $task_it->getId(),
                    array('Release' => $iteration_ids[0])
                );
                $task_it->moveNext();
            }
        }
        else
        {
            $task->add_parms(
                array (
                    'Assignee' => getSession()->getUserIt()->getId(),
                    'Planned' => $object_it->get('Estimation'),
                    'LeftWork' => $object_it->get('EstimationLeft'),
                    'TaskType' => getFactory()->getObject('TaskType')->getRegistry()->Query(
                        array (
                            new FilterBaseVpdPredicate(),
                            new TaskTypeStateRelatedPredicate($object_it->get('State'))
                        )
                    )->getId(),
                    'ChangeRequest' => $object_it->getId(),
                    'State' => array_pop($task->getTerminalStates()),
                    'Release' => $iteration_ids[0]
                )
            );
        }
    }
}
