<?php

class AccessRightUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$user = $model_factory->getObject('User');
 		
 		$user_it = $user->getExact($filter);
 		
 		if ( $user_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND EXISTS ( SELECT 1 FROM pm_ParticipantRole pr, pm_Participant n " .
			   "  			   WHERE t.ProjectRole = pr.ProjectRole" .
			   "    			 AND pr.Participant = n.pm_ParticipantId ".
 		       "                 AND n.SystemUser = ".$filter." ) ";
 	}
}
