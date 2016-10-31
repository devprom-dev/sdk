<?php

class ChangeLogDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			" IFNULL((SELECT p.Caption FROM cms_User p WHERE p.cms_UserId = t.SystemUser), t.Author) AuthorName " );

 		return $columns;
 	}
}
