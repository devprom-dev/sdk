<?php

class WikiTagFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = array_filter(preg_split('/[,-]/', $filter), function($value) {
 			return $value >= 0; 
 		});
 		
		if ( count($ids) < 1 || in_array('none', $ids) )
		{
			return " AND NOT EXISTS (SELECT 1 FROM WikiTag rt " .
				   "   		    	  WHERE rt.Wiki = t.WikiPageId) ";
		}
		else
		{
			$tag_it = getFactory()->getObject('Tag')->getExact($ids);
			
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
