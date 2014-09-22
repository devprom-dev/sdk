<?php

class UserRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		if ( $filter == 'all' ) return "";
 		
 		$role = $model_factory->getObject('ProjectRoleBase');
 		$role_it = $role->getExact($filter);

 		if ( $role_it->count() > 0 )
 		{
	 		$filter = "AND EXISTS ( SELECT 1 FROM pm_Participant r, pm_ParticipantRole o, pm_ProjectRole l " .
					  "			 	 WHERE t.cms_UserId = r.SystemUser AND r.pm_ParticipantId = o.Participant " .
					  "			   	   AND o.ProjectRole = l.pm_ProjectRoleId AND l.ProjectRoleBase = ".$role_it->getId().
					  "    		   	   ) ";
 		}
 		
 		return $filter;
 	}
}
