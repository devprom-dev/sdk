<?php

class RequestAuthorFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $users = array();
 	    $customers = array();

 	    array_walk(\TextUtils::parseIds($filter), function( $value, $index ) use (&$users, &$customers) {
 	       if ( $value < 1000000 && $value > 0 ) {
 	           $users[] = $value;
           }
 	       else {
 	           $customers[] = $value - 1000000;
           }
        });

 	    $sql = array();

 	    if ( count($users) > 0 ) {
            $sql[] = " t.Author IN (".join(',', $users).") ";
        }

        if ( count($customers) > 0 ) {
            $sql[] = " t.Customer IN (".join(',', $customers).") ";
        }

        if ( $this->hasNone($filter) ) {
            $sql[] = " t.Author IS NULL AND t.Customer IS NULL ";
        }
        if ( $this->hasAny($filter) ) {
            $sql[] = " (t.Author IS NOT NULL OR t.Customer IS NOT NULL) ";
        }

        return count($sql) > 0 ? " AND (".join(' OR ', $sql).") " : " AND 1 = 2 ";
 	}
}
