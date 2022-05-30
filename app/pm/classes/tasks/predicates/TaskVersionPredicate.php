<?php

class TaskVersionPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$stage_it = getFactory()->getObject('Stage')->getExact($filter);
			
 		if ( $stage_it->get('Release') > 0 ) {
 			return " AND t.Release = ".$stage_it->get('Release')." ";
 		}
 		
 		if ( $stage_it->get('Version') > 0 ) {
 			return " AND t.Release IN (SELECT r.pm_ReleaseId FROM pm_Release r WHERE r.Version = ".$stage_it->get('Version').") ";
 		}
 	}
}
