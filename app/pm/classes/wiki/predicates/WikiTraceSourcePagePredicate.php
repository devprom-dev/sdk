<?php

class WikiTraceSourcePagePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $likes = array();
        $ids = \TextUtils::parseIds($filter);
        foreach( $ids as $id ) {
            $likes[] = " p.ParentPath LIKE '%,".$id.",%' ";
        }
        if ( count($likes) < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 WikiPage p ".
               " 			  WHERE p.WikiPageId = t.SourcePage AND (".join(' OR ', $likes).") )";
 	}
}
