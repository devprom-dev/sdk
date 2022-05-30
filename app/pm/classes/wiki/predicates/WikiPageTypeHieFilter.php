<?php

class WikiPageTypeHieFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $refNames = array_map(
            function( $item ) {
                return "'{$item}'";
            },
            array_intersect(
                $this->getObject()->getTypeIt()->fieldToArray('ReferenceName'),
                \TextUtils::parseItems($filter)
            )
        );
 		if ( count($refNames) < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM WikiPage c, WikiPageType tp
                              WHERE FIND_IN_SET(t.WikiPageId, c.ParentPath)
                                AND c.PageType = tp.WikiPageTypeId
                                AND tp.ReferenceName IN (".join($refNames).")) ";
 	}
}
