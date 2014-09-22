<?php

class RequestTraceObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( !is_a($filter, 'OrderedIterator') ) return "AND 1 = 2 ";

 		if ( $filter->count() < 1 ) return " AND 1 = 2 ";
 		
		return " AND t.ObjectClass = '".strtolower(get_class($filter->object))."'" .
			   " AND t.ObjectId IN (".join(",", $filter->idsToArray()).")";
 	}
} 
