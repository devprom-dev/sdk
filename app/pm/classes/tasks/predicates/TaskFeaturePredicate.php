<?php

class TaskFeaturePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$ids = array_filter(preg_split('/,/', $filter), function($value) {
			return $value > 0;
		});
		if ( strpos($filter, 'none') !== false ) $ids[] = '0';
		if ( count($ids) < 1 ) return " AND 1 = 2 ";

		$sql = " EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest AND IFNULL(r.Function,0) IN (".join(',',$ids).")) ";
		if ( in_array(0, $ids)) {
			$sql = " ( ".$sql." OR NOT EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) ) ";
		}
		return " AND ".$sql;
 	}
}
