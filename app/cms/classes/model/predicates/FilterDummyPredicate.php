<?php

class FilterDummyPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('-');
	}
	
  	function _predicate( $filter )
 	{
 	    return " AND 1 = 1 ";
 	}
}
