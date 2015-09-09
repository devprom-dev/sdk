<?php

class FeatureStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		switch ( $filter )
		{
			case 'closed':
				return " AND IFNULL(t.EstimationLeft, 0) <= 0 ";

			case 'open':
			    return " AND IFNULL(t.EstimationLeft, 0) > 0 ";

			default:
				return '';
		}
 	}
}
