<?php

class WikiFileReferenceFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
 			   "			  WHERE p.WikiPageId = t.WikiPage" .
 			   "				AND p.ReferenceName = ".$filter." ) ";
 	}
}
