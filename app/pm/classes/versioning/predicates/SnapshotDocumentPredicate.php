<?php

class SnapshotDocumentPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $ids = \TextUtils::parseIds($filter);
 	    if ( count($ids) < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM WikiPage p1, WikiPage p2 
                              WHERE p1.WikiPageId IN (".join(',',$ids).") 
                                AND p1.UID = p2.UID AND t.ObjectId = p2.WikiPageId) ";
 	}
}
