<?php

class CustomReportCommonPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter ) {
 		return " AND t.IsPublic = 'Y' ";
 	}
}
