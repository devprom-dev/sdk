<?php

class RequestDetailsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = "(SELECT tp.Caption FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeName ";

 		return $columns;
 	}
}
