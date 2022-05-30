<?php

class StaleSearchableFilter extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter ) {
 		return " AND t.IsActive = 'N' AND NOW() - t.RecordModified > 10 ";
 	}
}
