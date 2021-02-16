<?php

class TaskTypePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" IFNULL((SELECT i.ReferenceName FROM pm_TaskType i WHERE i.pm_TaskTypeId = t.TaskType),'z') TaskTypeBase ",
            " ( SELECT r.State FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) IssueState "
 		);
 	}

	function map( & $parms )
	{
		if ( $parms['TaskTypeBase'] == '' ) return;
		if ( $parms['TaskTypeBase'] == 'z' ) {
			$parms['TaskType'] = 'NULL';
		}
		else {
			$parms['TaskType'] = getFactory()->getObject('TaskType')->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ReferenceName', $parms['TaskTypeBase']),
                    new FilterBaseVpdPredicate()
                )
			)->getId();
		}
		unset($parms['TaskTypeBase']);
	}

	function IsPersisterImportant() {
        return true;
    }
}

