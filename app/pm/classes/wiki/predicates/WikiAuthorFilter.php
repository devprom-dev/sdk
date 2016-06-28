<?php

class WikiAuthorFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$ids = array_filter(preg_split('/,/',$filter), function($value) {
			return $value > 0;
		});
		if ( count($ids) < 1 ) return " AND 1 = 1 ";

		$ids = getFactory()->getObject('User')->getExact($ids)->idsToArray();
		if ( count($ids) < 1 ) return " AND 1 = 2 ";

 		return " AND EXISTS (SELECT 1 FROM pm_Participant p WHERE p.pm_ParticipantId = t.Author AND p.SystemUser IN (".join(',',$ids)."))";
 	}
}
