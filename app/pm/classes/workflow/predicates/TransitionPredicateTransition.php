<?php

class TransitionPredicateTransition extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return " AND t.Transition = ".$filter;
 	}
}
