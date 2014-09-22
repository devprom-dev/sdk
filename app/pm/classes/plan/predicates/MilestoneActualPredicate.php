<?php

class MilestoneActualPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND IFNULL(Passed, 'N') = 'N' ";
 	}
}
