<?php

class TransitionAfterStatePredicate extends FilterPredicate
{
    function _predicate( $filter ) {
 		return " AND EXISTS (
 		            SELECT 1 FROM pm_State tar, pm_State sr
 		             WHERE tar.pm_StateId = t.TargetState
 		               AND sr.pm_StateId = {$filter}
 		               AND tar.OrderNum >= sr.OrderNum ) ";
 	}
}
