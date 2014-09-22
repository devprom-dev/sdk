<?php

class CommonAccessReportPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.ReferenceName = '".strtolower($filter)."'";
 	}
}
