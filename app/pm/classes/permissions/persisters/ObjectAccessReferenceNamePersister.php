<?php

class ObjectAccessReferenceNamePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  " CONCAT_WS('.', t.ObjectClass, t.ObjectId ) ReferenceName ";
 		
 		return $columns;
 	}
}
