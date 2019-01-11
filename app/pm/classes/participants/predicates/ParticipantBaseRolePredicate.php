<?php

class ParticipantBaseRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$role_it = getFactory()->getObject('ProjectRoleBase')->getExact($filter);
		if ( $role_it->count() > 1  ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole r, pm_ProjectRole prr " .
               "			  WHERE r.Participant = p.pm_ParticipantId" .
               "				AND p.SystemUser = ".$this->getAlias().".SystemUser" .
               "                AND p.VPD = ".$this->getAlias().".VPD" .
               "				AND r.ProjectRole = prr.pm_ProjectRoleId" .
               "				AND prr.ProjectRoleBase = ".$role_it->getId()." ) ";
 	}
}
