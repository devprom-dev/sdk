<?php

class StateRuleStateClassPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND EXISTS (SELECT 1 FROM pm_State s, pm_Transition tr " .
 			   "			  WHERE s.pm_StateId = tr.SourceState" .
 			   "				AND s.ObjectClass = '".$filter."' ".
               "                AND tr.pm_TransitionId = t.Transition) ";
 	}
}
