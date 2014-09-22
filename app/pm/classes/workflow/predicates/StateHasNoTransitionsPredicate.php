<?php

class StateHasNoTransitionsPredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}

 	function _predicate( $filter )
 	{
		return " AND NOT EXISTS (SELECT 1 FROM pm_Transition tr WHERE t.pm_StateId IN (tr.TargetState, tr.SourceState)) ";
 	}
} 
