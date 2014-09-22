<?php

class RequestLinkedFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( is_numeric($filter) )
 		{
 			return " AND ".$filter." IN (t.SourceRequest, t.TargetRequest) ";
 		}
 		else
 		{
 			return " AND 1 = 2 ";
 		}
 	}
}
