<?php

class AdminChangeLogPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			" TIMESTAMP(FROM_DAYS(TO_DAYS(t.RecordModified))) ChangeDate " );

 		return $columns;
 	}
}
