<?php

class ChangeLogItemFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( !is_a($filter, 'OrderedIterator') ) throw new Exception('Iterator is required');

 	    $created = $filter->fieldToArray('RecordCreated');
 	    
 	    $modified = $filter->fieldToArray('RecordModified');
 	    
 	    $date_created = strftime('%Y-%m-%d %H:%M:%S', strtotime('-5 minutes', strtotime(min($created))));
 	    
 	    $date_modified = strftime('%Y-%m-%d %H:%M:%S', strtotime('5 minutes', strtotime(max($modified))));
 	    
		return " AND t.ClassName = '".strtolower(get_class($filter->object))."' ".
		       " AND t.ObjectId IN (".join(',',$filter->idsToArray()).")".
		       " AND t.RecordModified BETWEEN '".$date_created."' AND '".$date_modified."' ";
 	}
}
