<?php

class TaskTraceClassPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.ObjectClass = '".strtolower($filter)."' ";
 	}
} 
