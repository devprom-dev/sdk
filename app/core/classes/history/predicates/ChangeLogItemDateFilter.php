<?php

class ChangeLogItemDateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !is_a($filter, 'OrderedIterator') ) throw new Exception('Iterator is required');

 	    $date_created = strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 minutes', strtotime(min($filter->fieldToArray('RecordCreated')))));

		return " AND t.ClassName = '".strtolower(get_class($filter->object))."' ".
		       " AND t.ObjectId IN (".join(',',$filter->idsToArray()).")".
		       " AND t.RecordModified >= '".$date_created."' ";
 	}
}
