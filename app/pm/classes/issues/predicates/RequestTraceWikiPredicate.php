<?php

class RequestTraceWikiPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $ids = \TextUtils::parseIds($filter);
        if ( count($ids) < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM WikiPage p 
                              WHERE FIND_IN_SET(t.ObjectId, p.ParentPath) > 0 
                                AND p.WikiPageId IN (".join(',', $ids).") ) ";
 	}
} 
