<?php

class RequestTaskStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;

 		$object = $model_factory->getObject('pm_Task');
 		
 		switch ( $filter )
 		{
 			case 'notresolved':
		 		return " AND EXISTS (SELECT 1 FROM pm_Task e " .
		 			   "			  WHERE e.ChangeRequest = t.pm_ChangeRequestId " .
		 			   "				AND e.State NOT IN ('".join($object->getTerminalStates(), "','")."') ) ";
 				
 			default:
		 		$state = $model_factory->getObject('TaskState');
		 		
		 		$it = $state->getByRefArray( array (
		 			'ReferenceName' => preg_split('/,/', $filter)
		 		));
		 		
		 		if ( $it->getId() < 1 )
		 		{
		 			return " AND 1 = 2 ";
		 		}
		 		
		 		return " AND EXISTS (SELECT 1 FROM pm_Task e " .
		 			   "			  WHERE e.ChangeRequest = t.pm_ChangeRequestId " .
		 			   "				AND e.State IN ('".join($it->fieldToArray('ReferenceName'),"','")."') ) ";
 		}
 	}
}