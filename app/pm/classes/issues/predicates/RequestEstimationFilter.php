<?php

class RequestEstimationFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'simple':
 				return " AND t.Estimation BETWEEN 0 AND 3 ";

 			case 'normal':
 				return " AND t.Estimation BETWEEN 4 AND 13 ";

 			case 'hard':
 				return " AND t.Estimation > 13 ";

 			case 'undefined':
 				return " AND t.Estimation IS NULL ";
 				
 			default:
 				return "";
 		}
 	}
}
