<?php

class RequestStagePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$stage = $model_factory->getObject('Stage');
 		
 		$stage_it = $stage->getExact($filter);
 		
 		if ( $stage_it->get('Release') > 0 )
 		{
			return " AND EXISTS (SELECT 1 FROM pm_Task s " .
				   "			  WHERE t.pm_ChangeRequestId = s.ChangeRequest" .
				   "			    AND s.Release = ".$stage_it->get('Release').") ";
 		}
 		elseif ( $stage_it->get('Version') > 0 )
 		{
			return " AND t.PlannedRelease = ".$stage_it->get('Version');
 		}
 	}
}
