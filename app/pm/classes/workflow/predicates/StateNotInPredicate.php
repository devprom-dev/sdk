<?php

class StateNotInPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $states = array_filter(\TextUtils::parseFilterItems($filter, ',-'), function($state) {
            return preg_match('/[A-Za-z0-9_]/', $state);
        });
        if ( count($states) > 0 ) {
            return " AND ".$this->getAlias().".State NOT IN ('".join($states,"','")."')";
        }
        else {
            return " AND 1 = 2 ";
        }
 	}
}