<?php

class UserParticipanceRoleBasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$role_it = getFactory()->getObject('ProjectRoleBase')->getExact(\TextUtils::parseIds($filter));
		if ( $role_it->count() < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM pm_Participant n, pm_ParticipantRole r, pm_ProjectRole pr " .
               "			  WHERE n.SystemUser = t.cms_UserId ".
               "                AND r.Participant = n.pm_ParticipantId AND r.VPD = n.VPD " .
               "                AND r.ProjectRole = pr.pm_ProjectRoleId " .
               "				AND pr.ProjectRoleBase IN (".join($role_it->idsToArray(),',').") ) ";
 	}
}
