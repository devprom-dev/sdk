<?php

class UserParticipanceRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$role = getFactory()->getObject('ProjectRole');

		$role_it = $role->getExact(preg_split('/,/', $filter));
		if ( $role_it->count() > 0 )
		{
			return " AND EXISTS (SELECT 1 FROM pm_Participant n, pm_ParticipantRole r " .
				   "			  WHERE n.SystemUser = t.cms_UserId ".
				   "                AND r.Participant = n.pm_ParticipantId AND r.VPD = n.VPD " .
				   "				AND r.ProjectRole IN (".join($role_it->idsToArray(),',').") ) ";
		}
 	}
}
