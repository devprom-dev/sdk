<?php

class ObjectAccessReferenceNamePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();

		$columns[] =  " CONCAT_WS('.', t.ObjectClass, t.ObjectId ) ReferenceName ";
 		$columns[] =  " (SELECT r.Caption FROM pm_ProjectRole r WHERE r.pm_ProjectRoleId = t.ProjectRole) ProjectRoleName ";
 		
 		return $columns;
 	}
}
