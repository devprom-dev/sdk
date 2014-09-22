<?php

class TaskTypeNonBugFixPredicate extends FilterPredicate
{
 	function TaskTypeNonBugFixPredicate()
 	{
 		parent::FilterPredicate('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ReferenceName NOT IN ('support', 'accept') ";
 	}
}
