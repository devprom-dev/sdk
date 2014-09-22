<?php

class RequestVersionFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.ClosedInVersion LIKE '".$filter."%' ";
 	}

 	function get( $filter )
 	{
 		$instance = new RequestVersionFilter( $filter );
 		
 		return $instance->getPredicate();
 	}
}
