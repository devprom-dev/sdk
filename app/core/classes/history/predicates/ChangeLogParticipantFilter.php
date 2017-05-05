<?php

class ChangeLogParticipantFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
            case 'external':
 				return " AND t.SystemUser IS NULL ";
 			
 			case 'notme':
 				return " AND IFNULL(t.SystemUser, 0) <> ".getSession()->getUserIt()->getId();
 				
 			default:
		 		$ids = array_filter(preg_split('/,/',$filter), function($value) {
		 			return $value > 0;
		 		});
		 		if ( count($ids) < 1 ) return " AND 1 = 2 ";
		 		
		 		$user_it = getFactory()->getObject('User')->getExact($ids);
		 		if ( $user_it->count() < 1 ) return " AND 1 = 2 ";
		 		
		 		return " AND t.SystemUser IN (".join($user_it->idsToArray(),',').")";
 		}
 	}
}