<?php

class ParticipantBaseRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
		$role = $model_factory->getObject('ProjectRoleBase');
		$role_it = $role->getExact($filter);

		if ( $role_it->count() > 0 )
		{
			return " AND EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole r, pm_ProjectRole prr " .
				   "			  WHERE r.Participant = p.pm_ParticipantId" .
				   "				AND p.SystemUser = ".$this->getAlias().".SystemUser" .
				   "				AND r.ProjectRole = prr.pm_ProjectRoleId" .
				   "				AND prr.ProjectRoleBase = ".$role_it->getId()." ) ";
		}
 	}
}
