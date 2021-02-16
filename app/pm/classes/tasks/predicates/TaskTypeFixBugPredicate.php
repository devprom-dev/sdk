<?php

class TaskTypeFixBugPredicate extends FilterPredicate
{
 	function TaskTypeFixBugPredicate() {
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ReferenceName IN ('support', 'accept') ";
 	}
}
