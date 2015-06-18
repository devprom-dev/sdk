<?php

class ObjectStatePredicate extends FilterPredicate
{
 	function _predicate( $state_it )
 	{
		return " AND t.State = '".$state_it->get('ReferenceName')."' ";
 	}
} 
