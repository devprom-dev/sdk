<?php

class AccessRightTypeNonePredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}

	function _predicate( $filter ) {
 		return " AND t.AccessType IN ('none') ";
 	}
}
