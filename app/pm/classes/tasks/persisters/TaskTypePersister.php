<?php

class TaskTypePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" IFNULL((SELECT i.ReferenceName FROM pm_TaskType i WHERE i.pm_TaskTypeId = t.TaskType),'z') TypeBase "
 		);
 	}

	function map( & $parms )
	{
		if ( $parms['TypeBase'] == '' ) return;
		if ( $parms['TypeBase'] == 'z' ) {
			$parms['TaskType'] = 'NULL';
		}
		else {
			$parms['TaskType'] = getFactory()->getObject('TaskType')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('ReferenceName', $parms['TypeBase']),
							new FilterBaseVpdPredicate()
					)
			)->getId();
		}
	}
}

