<?php

class RequestOwnerPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT tp.SystemUser FROM pm_Participant tp WHERE tp.pm_ParticipantId = t.Owner ) OwnerUser ";
 		
 		return $columns;
 	}
}
