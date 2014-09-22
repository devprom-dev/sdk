<?php

class TaskAssigneePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " (SELECT p.SystemUser FROM pm_Participant p WHERE p.pm_ParticipantId = t.Assignee) AssigneeUser ";
 		
 		return $columns;
 	}
 	
 	function modify( $object_id, $parms )
 	{
 		if ( !array_key_exists('AssigneeUser', $parms) ) return; 
 		
 		$user_id = trim($parms['AssigneeUser']);
 		
 		if ( $user_id < 1 )
 		{
 			$this->getObject()->modify_parms( $object_id, 
 					array (
 							'Assignee' => 'NULL'
 					)
 			);
 		}
 		else
 		{
 			$this->getObject()->modify_parms( $object_id, 
 					array (
 							'Assignee' => getFactory()->getObject('Participant')->getRegistry()->Query(
				 									array (
				 											new FilterAttributePredicate('SystemUser', $user_id),
				 											new FilterAttributePredicate('IsActive', 'Y'),
				 											new FilterBaseVpdPredicate()
				 									)
				 							)->getId()
 					)
 			);
 		}
 	}
}

