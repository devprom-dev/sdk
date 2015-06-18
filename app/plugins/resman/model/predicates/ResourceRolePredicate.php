<?php

class ResourceRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( $filter == 'all' ) return "";

		$role_it = getFactory()->getObject('ProjectRoleBase')->getExact($filter);

		return $role_it->count() > 0 ? " AND prr.ProjectRoleBase = ".$role_it->getId() : " AND 1 = 2 ";
 	}
}
