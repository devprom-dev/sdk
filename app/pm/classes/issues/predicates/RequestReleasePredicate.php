<?php

class RequestReleasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$release_it = getFactory()->getObject('Release')->getExact($filter);
 		
 		if ( $release_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND ( t.PlannedRelease IN (".join(',',$release_it->idsToArray()).") ".
 			   "       OR EXISTS (SELECT 1 FROM pm_Release r, pm_Task s ".
 			   "				   WHERE r.Version IN (".join(',',$release_it->idsToArray()).") ".
 			   "					 AND r.pm_ReleaseId = s.Release AND s.ChangeRequest = t.pm_ChangeRequestId) )";
 	}
} 
