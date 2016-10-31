<?php

class RequestEstimationFilter extends FilterPredicate
{
	function _predicate( $filter )
	{
		$parts = preg_split('/:/', $filter);
		if ( count($parts) > 1 ) {
			$left = is_numeric($parts[0]) ? $parts[0] : 0;
			$right = is_numeric($parts[1]) ? $parts[1] : 0;
			return " AND t.Estimation BETWEEN ".$left." AND ".$right." ";
		}
		else {
			if ( is_numeric($parts[0]) ) {
				return " AND t.Estimation > ".$parts[0];
			}
			else {
				return " AND t.Estimation IS NULL ";
			}
		}
	}
}
