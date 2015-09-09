<?php

class StateActionReferencePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.ReferenceName = '".$filter."' ";
 	}
} 
