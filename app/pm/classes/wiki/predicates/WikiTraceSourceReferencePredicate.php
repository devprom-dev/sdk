<?php

class WikiTraceSourceReferencePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
			   "			  WHERE p.WikiPageId = t.SourcePage AND p.ReferenceName = ".strtolower($filter)." ) ";
 	}
} 
