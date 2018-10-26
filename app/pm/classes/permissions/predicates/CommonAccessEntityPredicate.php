<?php

class CommonAccessEntityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $classes = array_filter(
            preg_split('/,/', $filter),
            function( $value ) {
                return preg_match('/[a-zA-Z]+/', $value);
            }
        );
 	    if ( count($classes) < 1 ) return " AND 1 = 2 ";

		return " AND t.ReferenceName IN ('".join("','", $classes)."') ";
 	}
}
