<?php

class WikiDocumentWaitFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $ids = TextUtils::parseIds($filter);
 	    if ( count($ids) < 1 ) return " AND 1 = 2 ";
 		return " AND IFNULL(t.DocumentId,".array_shift(array_values($ids)).") IN (".join(',',$ids).") ";
 	}
}
