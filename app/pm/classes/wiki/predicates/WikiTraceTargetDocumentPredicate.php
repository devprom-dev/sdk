<?php

class WikiTraceTargetDocumentPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
			   "			  WHERE p.WikiPageId = t.TargetPage AND p.DocumentId = ".$filter." ) ";
 	}
} 
