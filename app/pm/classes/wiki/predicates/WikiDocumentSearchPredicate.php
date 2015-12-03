<?php

class WikiDocumentSearchPredicate extends FilterSearchAttributesPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM WikiPage p
				 			  WHERE p.ParentPath LIKE CONCAT('%,',t.WikiPageId,',%') ".parent::_predicate($filter).")";
 	}
}
