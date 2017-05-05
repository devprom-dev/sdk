<?php

class ChangeLogDocumentFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( !is_numeric($filter) ) return " AND 1 = 2 ";
 		return " AND t.EntityRefName = 'WikiPage' 
 		         AND EXISTS (SELECT 1 FROM WikiPage p 
 		                        WHERE p.WikiPageId = t.ObjectId AND p.DocumentId = ".$filter.")";
 	}
}
