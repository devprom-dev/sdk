<?php

class ObjectStatePredicate extends FilterPredicate
{
 	function _predicate( $state_it )
 	{
 		if ( array_shift($this->getObject()->getStates()) == $state_it->get('ReferenceName') )
 		{
 			// very first state
 			return " AND t.StateObject IS NULL ";
 		}
 		else
 		{
			return " AND t.State = '".$state_it->get('ReferenceName')."' ";
 		}
 	}
} 
