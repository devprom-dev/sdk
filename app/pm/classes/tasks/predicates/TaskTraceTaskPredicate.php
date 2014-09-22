<?php

class TaskTraceTaskPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.Task = ".$filter;
 	}
} 
