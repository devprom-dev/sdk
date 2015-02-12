<?php

class TaskAssigneePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = " t.Assignee AssigneeUser ";
 		
 		return $columns;
 	}
}

