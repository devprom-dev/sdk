<?php

class RequestTaskTypePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$object = $model_factory->getObject('pm_TaskType');
 		
 		$it = $object->getByRefArray( array (
 			'ReferenceName' => preg_split('/,/', $filter)
 		));
 		
 		if ( $it->getId() < 1 )
 		{
 			return " AND 1 = 2 ";
 		}

 		return " AND EXISTS (SELECT 1 FROM pm_Task e " .
 			   "			  WHERE e.ChangeRequest = t.pm_ChangeRequestId " .
 			   "				AND e.TaskType IN (".join($it->idsToArray(),',').")) ";
 	}
}
