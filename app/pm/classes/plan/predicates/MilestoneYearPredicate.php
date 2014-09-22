<?php

class MilestoneYearPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND YEAR(MilestoneDate) = ".$filter;
 	}
}
