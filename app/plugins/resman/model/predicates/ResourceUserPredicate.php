<?php

class ResourceUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND p.SystemUser = ".$filter;
 	}
}
