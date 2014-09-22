<?php

class TaskTypeFixBugPredicate extends FilterPredicate
{
 	function TaskTypeFixBugPredicate()
 	{
 		parent::FilterPredicate('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.ReferenceName IN ('support', 'accept') ";
 	}
}
