<?php

class RequestSubmittedFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.SubmittedVersion LIKE '%".$filter."%' ";
 	}
}
