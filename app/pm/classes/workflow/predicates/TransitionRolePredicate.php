<?php

class TransitionRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.Transition = ".$filter;
 	}
}
