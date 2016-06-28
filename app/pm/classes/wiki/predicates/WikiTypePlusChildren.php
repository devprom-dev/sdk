<?php

class WikiTypePlusChildren extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$type_it = getFactory()->getObject('WikiTypeBase')->getRegistry()->Query(
			array (
				new FilterVpdPredicate(),
				new FilterAttributePredicate('ReferenceName', $filter)
			)
		);
		if ( $type_it->count() < 1 ) return " AND 1 = 2 ";

 		return " AND EXISTS (SELECT 1 FROM WikiPage p " .
 			   "			  WHERE p.PageType IN (".join(',',$type_it->idsToArray()).") ".
		       " 			    AND t.ParentPath LIKE CONCAT('%,',p.WikiPageId,',%' )) ";
 	}
}
