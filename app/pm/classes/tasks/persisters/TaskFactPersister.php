<?php

class TaskFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
 			"(SELECT SUM(ac.Capacity) FROM pm_Activity ac WHERE ac.Task = {$this->getPK($alias)} ) Fact "
		);
 	}

 	function modify($object_id, $parms)
    {
        $this->processFact($object_id, $parms);
        parent::modify($object_id, $parms);
    }

    function add($object_id, $parms)
    {
        $this->processFact($object_id, $parms);
        parent::add($object_id, $parms);
    }

    function processFact($object_id, $parms)
    {
        if ( $parms['Fact'] <= 0 ) return;

        $objectIt = $this->getObject()->getExact($object_id);
        $factDelta = max(0, $parms['Fact'] - $objectIt->get('Fact'));
        if ( $factDelta <= 0 ) return;

        $registry = getFactory()->getObject('ActivityTask')->getRegistry();
        $activityIt = $registry->Query(
            array(
                new FilterAttributePredicate('Task', $object_id),
                new FilterAttributePredicate('ReportDate', SystemDateTime::date('Y-m-d')),
            )
        );
        if ( $activityIt->getId() == '' ) {
            $registry->Create(array(
                'Task' => $object_id,
                'ReportDate' => SystemDateTime::date('Y-m-d'),
                'Capacity' => $factDelta,
                'Participant' => $objectIt->get('Assignee')
            ));
        }
        else {
            $registry->Store($activityIt, array(
                'Capacity' => $activityIt->get('Capacity') + $factDelta
            ));
        }
    }
}
