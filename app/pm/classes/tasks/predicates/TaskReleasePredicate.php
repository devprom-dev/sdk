<?php

class TaskReleasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $clauses = array();

 		$release_it = getFactory()->getObject('Release')->getExact(TextUtils::parseIds($filter));
 		if ( $release_it->count() > 0 ) {
            $clauses[] = " t.Release IN (SELECT r.pm_ReleaseId FROM pm_Release r WHERE r.Version IN (".join(',',$release_it->idsToArray()).")) ";
            $clauses[] = " t.ChangeRequest IN (SELECT r.pm_ChangeRequestId FROM pm_ChangeRequest r WHERE r.PlannedRelease IN (".join(',',$release_it->idsToArray()).")) ";
        }

 		if ( strpos($filter, 'none') !== false ) {
            $clauses[] = " t.Release IS NULL AND (t.ChangeRequest IS NULL OR t.ChangeRequest IN (SELECT r.pm_ChangeRequestId FROM pm_ChangeRequest r WHERE r.PlannedRelease IS NULL)) ";
        }

        return count($clauses) < 1 ? " AND 1 = 2 " : " AND ( ".join(' OR ', $clauses)." ) ";
 	}
}
