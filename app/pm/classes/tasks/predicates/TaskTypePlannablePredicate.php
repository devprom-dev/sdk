<?php

class TaskTypePlannablePredicate extends FilterPredicate
{
 	function TaskTypePlannablePredicate()
 	{
 		parent::FilterPredicate('default');
 	}
 	
 	function _predicate( $filter )
 	{
 		return " AND t.UsedInPlanning = 'Y'";
 	}
}
