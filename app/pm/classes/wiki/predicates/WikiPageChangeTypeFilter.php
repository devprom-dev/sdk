<?php

class WikiPageChangeTypeFilter extends FilterPredicate
{
 	function _predicate( $filter )
    {
 	    if ( !is_numeric($filter) ) return " AND 1 = 2 ";
 		return " AND EXISTS (SELECT 1 FROM WikiPage p WHERE p.WikiPageId = t.WikiPage AND p.ReferenceName = ".$filter.".) ";
 	}
}
 