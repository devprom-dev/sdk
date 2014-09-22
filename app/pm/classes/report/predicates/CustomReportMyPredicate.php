<?php

class CustomReportMyPredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
 		$user_id = getSession()->getUserIt()->getId();
 		
 		return " AND t.Author IN (".($user_id > 0 ? $user_id : '0').", -1) ";
 	}
}
