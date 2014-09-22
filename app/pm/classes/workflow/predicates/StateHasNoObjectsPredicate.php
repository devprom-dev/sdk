<?php

class StateHasNoObjectsPredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}

 	function _predicate( $filter )
 	{
		return " AND NOT EXISTS (SELECT 1 FROM pm_StateObject o WHERE o.State = t.pm_StateId) ";
 	}
} 
