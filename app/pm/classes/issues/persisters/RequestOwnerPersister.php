<?php

// obsolete
class RequestOwnerPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " t.Owner OwnerUser ";
 		
 		return $columns;
 	}
}
