<?php

class ChangeLogExceptParticipantFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$part = $model_factory->getObject('pm_Participant');
 		$part_it = $part->getExact( $filter );
 		
 		if ( $part_it->count() < 1 ) return " AND 1 = 2 ";
 		
		return " AND IFNULL(t.Author, 0) <> ".$filter;
 	}
}
