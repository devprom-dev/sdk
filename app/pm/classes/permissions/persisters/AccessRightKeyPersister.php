<?php

class AccessRightKeyPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
 		array_push( $columns, 
 			" CONCAT_WS(',',t.ReferenceName,t.ReferenceType,t.ProjectRole) RecordKey " );

 		return $columns;
 	}
}
 
