<?php

class StateClassPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		if ( $filter == '' ) return " AND 1 = 1 ";
		return " AND t.ObjectClass = '".strtolower($filter)."' ";
 	}
} 
