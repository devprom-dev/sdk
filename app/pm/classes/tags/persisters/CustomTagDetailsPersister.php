<?php

class CustomTagDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " ( SELECT g.Caption FROM Tag g WHERE g.TagId = t.Tag ) Caption ";
 		$columns[] = " t.Tag TagId ";
 		
 		return $columns;
 	}
} 