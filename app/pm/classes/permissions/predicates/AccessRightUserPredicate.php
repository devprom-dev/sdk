<?php

class AccessRightUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user_it = getFactory()->getObject('User')
            ->getExact(\TextUtils::parseFilterItems($filter));
 		
 		if ( $user_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND EXISTS ( SELECT 1 FROM pm_ParticipantRole pr, pm_Participant n " .
			   "  			   WHERE t.ProjectRole = pr.ProjectRole" .
			   "    			 AND pr.Participant = n.pm_ParticipantId ".
 		       "                 AND n.SystemUser = ".$filter." ) ";
 	}
}
