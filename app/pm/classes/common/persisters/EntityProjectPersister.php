<?php

class EntityProjectPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		array_push( $columns, 
 			"(SELECT r.pm_ProjectId FROM pm_Project r WHERE r.VPD = t.VPD LIMIT 1) Project " );

 		return $columns;
 	}
}
