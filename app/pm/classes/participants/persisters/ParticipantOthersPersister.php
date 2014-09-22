<?php

class ParticipantOthersPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$object = $this->getObject();
 		$predicate = $object->getVpdPredicate('p'); 
 		
 		array_push( $columns, 
 			"( SELECT GROUP_CONCAT(p.pm_ParticipantId) ".
 		    "	 FROM pm_Participant p ".
 			"   WHERE p.SystemUser = t.SystemUser ".$predicate." ) OtherIds " );

 		return $columns;
 	}
}
