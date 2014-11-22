<?php

class ChangeLogActionFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$types = array_intersect(
 				getFactory()->getObject('ChangeLogAction')->getAll()->fieldToArray('ReferenceName'),
 				preg_split('/,/', $filter)
		); 
 		
 		if ( in_array('commented', $types) )
 		{
 			$types[] = 'comment_modified';
 			$types[] = 'comment_deleted';
 		}
 		
 		if ( count($types) < 1 ) return " AND 1 = 2 ";
 		
 		return " AND t.ChangeKind IN ('".join("','", $types)."') ";
 	}
}
