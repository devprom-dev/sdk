<?php

class ObjectStatePredicate extends FilterPredicate
{
 	function _predicate( $state_it )
 	{
		return " AND t.StateObject IN (SELECT so.pm_StateObjectId FROM pm_StateObject so WHERE so.State = ".$state_it->getId().") ";
 	}
} 
