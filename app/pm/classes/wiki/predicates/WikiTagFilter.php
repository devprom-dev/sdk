<?php

class WikiTagFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = TextUtils::parseIds($filter);
 		
		if ( count($ids) < 1 || in_array('none', $ids) )
		{
			return " AND NOT EXISTS (SELECT 1 FROM WikiTag rt " .
				   "   		    	  WHERE rt.Wiki = t.WikiPageId) ";
		}
		else
		{
			$tag_it = getFactory()->getObject('Tag')->getExact($ids);
			if ( $tag_it->count() < 1 ) return " AND 1 = 1 ";
			
			if ( in_array('0', $ids) )
			{
				return " AND (EXISTS (SELECT 1 FROM WikiTag rt " .
					   "   		      WHERE rt.Wiki = t.WikiPageId " .
					   "                AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ".
					   "	  OR NOT EXISTS (SELECT 1 FROM WikiTag wt WHERE wt.Wiki = t.WikiPageId)) ";
			}
			else
			{
				return " AND EXISTS (SELECT 1 FROM WikiTag rt " .
					   "   		      WHERE rt.Wiki = t.WikiPageId " .
					   "                AND rt.Tag IN (".join($tag_it->idsToArray(),',').")) ";
			}
		}
 	}
}
