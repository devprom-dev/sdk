<?php

class FilterEmptyPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('empty');
    }

    function _predicate( $filter ) {
 	    return " AND 1 = 2 ";
 	}
}
