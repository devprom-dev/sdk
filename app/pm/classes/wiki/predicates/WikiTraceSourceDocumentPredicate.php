<?php

class WikiTraceSourceDocumentPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
			   "			  WHERE p.WikiPageId = t.SourcePage AND p.DocumentId = ".$filter." ) ";
 	}
} 
