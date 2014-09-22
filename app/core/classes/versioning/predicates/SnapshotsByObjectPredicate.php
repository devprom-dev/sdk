<?php

class SnapshotsByObjectPredicate extends FilterPredicate
{
    function _predicate( $filter )
    {
    	if ( !is_a($filter, 'OrderedIterator') ) return " AND 1 = 2 ";
    	
        return " AND EXISTS (SELECT 1 FROM cms_SnapshotItem i ".
               "              WHERE i.Snapshot = t.cms_SnapshotId ".
               "                AND i.ObjectId IN (".join(',',$filter->idsToArray()).") ".
               "                AND i.ObjectClass = '".get_class($filter->object)."') ";
    }
}