<?php

class FilterNoVpdPredicate extends FilterPredicate
{
 	function __construct( $filter = 'base' )
 	{
 		parent::__construct($filter);
 	}
 	
 	function _predicate( $filter = '' )
 	{
 	    if ( $filter == 'base' ) {
            return " AND t.VPD IS NULL ";
        }
        else {
            return " AND t.VPD NOT IN ('".$filter."') ";
        }
 	}
}
