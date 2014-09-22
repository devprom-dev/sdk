<?php

class TransitionStateClassPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND EXISTS (SELECT 1 FROM pm_State s " .
 			   "			  WHERE s.pm_StateId = t.SourceState" .
 			   "				AND s.ObjectClass = '".$filter."') ";
 	}
}
