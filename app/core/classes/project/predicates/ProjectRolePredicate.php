<?php

class ProjectRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$user = $model_factory->getObject('ProjectRoleBase');
 		$user_it = $user->getExact($filter);
 		
 		if ( $user_it->count() > 0 )
 		{
	 		$filter = "AND EXISTS ( SELECT 1 FROM pm_Participant r, pm_ParticipantRole o, pm_ProjectRole l " .
					  "			 	 WHERE t.pm_ProjectId = r.Project AND r.pm_ParticipantId = o.Participant " .
					  "			   	   AND o.ProjectRole = l.pm_ProjectRoleId AND l.ProjectRoleBase = ".$user_it->getId().
					  "    		   )";

 			return $filter;
 		}
 	}
}
