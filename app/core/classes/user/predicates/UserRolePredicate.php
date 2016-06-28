<?php

class UserRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$ids = array_filter(preg_split('/,/',$filter), function($value) {
			return $value > 0;
		});
		if ( count($ids) < 1 ) return " AND 1 = 2 ";
 		
 		$role_it = getFactory()->getObject('ProjectRoleBase')->getExact($ids);
 		if ( $role_it->count() < 1 ) return " AND 1 = 2 ";

		return    " AND EXISTS ( SELECT 1 FROM pm_Participant r, pm_ParticipantRole o, pm_ProjectRole l " .
				  "			 	 WHERE t.cms_UserId = r.SystemUser AND r.pm_ParticipantId = o.Participant " .
				  "			   	   AND o.ProjectRole = l.pm_ProjectRoleId AND l.ProjectRoleBase IN ( ".join(',',$role_it->idsToArray()).") ".
				  "    		   	   ) ";
 	}
}
