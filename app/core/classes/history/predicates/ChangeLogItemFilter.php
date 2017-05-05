<?php

class ChangeLogItemFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !is_a($filter, 'OrderedIterator') ) throw new Exception('Iterator is required');

		return " AND t.ClassName = '".strtolower(get_class($filter->object))."' ".
		       " AND t.ObjectId IN (".join(',',$filter->idsToArray()).")";
 	}
}
