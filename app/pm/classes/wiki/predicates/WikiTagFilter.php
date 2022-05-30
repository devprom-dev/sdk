<?php

class WikiTagFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $sqls = array();

        if ( count(array_intersect(TextUtils::parseItems($filter), array('0', 'none'))) > 0 ) {
            $sqls[] = " NOT EXISTS (
                            SELECT 1 FROM WikiTag rt, Tag t 
                                WHERE rt.Wiki = t.WikiPageId
                                  AND rt.Tag = t.TagId ) ";
        }

        if ( in_array('any', TextUtils::parseItems($filter)) ) {
            $sqls[] = " EXISTS (
                            SELECT 1 FROM WikiTag rt, Tag t 
                                WHERE rt.Wiki = t.WikiPageId
                                  AND rt.Tag = t.TagId ) ";
        }

        $tag = getFactory()->getObject('Tag');
        $tag_it = $tag->getExact( TextUtils::parseIds($filter) );
        if ( $tag_it->count() > 0 ) {
            $sqls[] = " EXISTS (
                            SELECT 1 FROM WikiTag rt 
                             WHERE rt.Wiki = t.WikiPageId 
                               AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ";
        }

        if ( count($sqls) < 1 ) return " AND 1 = 2 ";

        return " AND (" . join(' OR ', $sqls) . ")";
 	}
}
