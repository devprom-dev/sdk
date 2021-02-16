<?php

class WikiTraceSourceDocumentPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $ids = \TextUtils::parseIds($filter);
 	    if ( count($ids) < 1 ) return " AND 1 = 1 ";

		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
			   "			  WHERE p.WikiPageId = t.SourcePage AND p.DocumentId IN (".join(',',$ids).") ) ";
 	}
} 
