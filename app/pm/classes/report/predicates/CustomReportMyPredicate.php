<?php

class CustomReportMyPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
 		$user_id = getSession()->getUserIt()->getId();
 		return " AND (t.Author = ".($user_id > 0 ? $user_id : '0')." AND t.IsPublic = 'N' OR t.IsPublic = 'Y') ";
 	}
}
