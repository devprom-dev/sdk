<?php

class CommonAccessRolePredicate extends FilterPredicate
{
	function _predicate( $filter )
	{
		$role_it = getFactory()->getObject('pm_ProjectRole')->getExact(preg_split('/,/', $filter));
		if ( $role_it->count() < 1 ) return " AND 1 = 2 ";

		return " AND pm_ProjectRoleId IN (".join(',',$role_it->idsToArray()).")";
	}
}
