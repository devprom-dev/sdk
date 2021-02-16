<?php

class TaskTypeNonBugFixPredicate extends FilterPredicate
{
 	function TaskTypeNonBugFixPredicate() {
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ReferenceName NOT IN ('support', 'accept') ";
 	}
}
