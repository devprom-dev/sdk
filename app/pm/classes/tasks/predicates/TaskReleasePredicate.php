<?php

class TaskReleasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$release_it = getFactory()->getObject('Release')->getExact(preg_split('/,/', $filter));
 		
 		if ( $release_it->getId() < 1 ) return " AND 1 = 2 ";

 		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
 		{
 			return " AND t.Release IN (SELECT r.pm_ReleaseId FROM pm_Release r WHERE r.Version IN (".join(',',$release_it->idsToArray()).")) ";
 		}
 		else
 		{
 			return " AND t.ChangeRequest IN (SELECT r.pm_ChangeRequestId FROM pm_ChangeRequest r WHERE r.PlannedRelease IN (".join(',',$release_it->idsToArray()).")) ";
 		}
 	}
}
