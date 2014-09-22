<?php

class ChangeLogParticipantFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
 				return " AND t.SystemUser IS NULL ";
 			
 			case 'notme':
 				return " AND t.SystemUser <> ".getSession()->getUserIt()->getId();
 				
 			default:
		 		$part = getFactory()->getObject('pm_Participant');
		 		
		 		$ids = array_filter(preg_split('/,/',$filter), function($value) {
		 			return $value > 0;
		 		});
		 		
		 		if ( count($ids) < 1 ) return " AND 1 = 2 ";
		 		
		 		$part_it = $part->getExact($ids);
		 		
		 		if ( $part_it->count() < 1 ) return " AND 1 = 2 ";
		 		
		 		return " AND t.Author IN (".join($part_it->idsToArray(),',').")";
 		}
 	}
}