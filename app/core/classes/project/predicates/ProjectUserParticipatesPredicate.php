<?php

class ProjectUserParticipatesPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user_it = getFactory()->getObject('cms_User')
            ->getExact(\TextUtils::parseFilterItems($filter));

 		if ( $user_it->count() < 1 ) return " AND 1 = 2 ";

	 	return " AND EXISTS (
	 	            SELECT 1 FROM pm_Participant r 
	 	             WHERE t.pm_ProjectId = r.Project 
                       AND r.SystemUser = ".$user_it->getId()." ) ";
 	}
}
