<?php
include_once "ObjectSQLPersister.php";

class ObjectRecordAgePersister extends ObjectSQLPersister
{
 	function getSelectColumns($alias)
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		array_push( $columns, " (TO_DAYS(NOW()) - TO_DAYS(".$alias."RecordModified)) AgeDays " ); 
 		
 		return $columns;
 	}
}
