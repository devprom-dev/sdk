<?php

class TaskTagsPersister extends ObjectSQLPersister
{
 	var $column_name = 'Tags';
 	
 	function getSelectColumns( $alias )
 	{
 		$columns[] =
 			"'' ".$this->column_name." ";

		$columns[] =
			"'' TagNames ";

 		return $columns;
 	}
}
