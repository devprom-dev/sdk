<?php

class RequestReleasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = preg_split('/,/',$filter);
 		
 		array_walk($ids, function(&$value) {
 			$value = is_numeric($value) && $value > 0 ? $value : '0';
 		});

 		if ( count($ids) < 1 ) return "AND 1 = 2";

 		return " AND ( IFNULL(t.PlannedRelease,0) IN (".join(',',$ids).") ".
 			   "       OR EXISTS (SELECT 1 FROM pm_Release r, pm_Task s ".
 			   "				   WHERE IFNULL(r.Version, 0) IN (".join(',',$ids).") ".
 			   "					 AND r.pm_ReleaseId = s.Release AND s.ChangeRequest = t.pm_ChangeRequestId) )";
 	}
} 
