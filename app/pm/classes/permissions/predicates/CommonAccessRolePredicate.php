<?php

class CommonAccessRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$role = $model_factory->getObject('pm_ProjectRole');
 		$role_it = $role->getExact( $filter );
 		
 		if ( $role_it->count() > 0 )
 		{
 			return " AND pm_ProjectRoleId = ".$role_it->getId();
 		}
 	}
}
