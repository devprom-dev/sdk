<?php

class FilterNextSiblingsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( $filter->getId() < 1 ) return " AND 1 = 2 ";
 		return " AND t.OrderNum >= ".($filter->get('OrderNum') > 0 ? $filter->get('OrderNum') : "0");
 	}
}
