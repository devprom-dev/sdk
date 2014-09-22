<?php

class TaskTraceObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !is_a($filter, 'OrderedIterator') ) return " AND 1 = 2 ";
 	    
		return " AND t.ObjectId IN (".join(",", $filter->idsToArray()).") ".
		       " AND t.ObjectClass = '".strtolower(get_class($filter->object))."' ";
 	}
} 
