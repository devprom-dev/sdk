<?php

class StateTransitionTargetPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		if ( !is_numeric($filter) ) $filter = 0;
 		
		return " AND t.pm_StateId IN (SELECT tr.TargetState FROM pm_Transition tr WHERE tr.pm_TransitionId = ".$filter.") ";
 	}
} 
