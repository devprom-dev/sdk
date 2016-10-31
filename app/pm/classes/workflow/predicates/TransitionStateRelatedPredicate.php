<?php

class TransitionStateRelatedPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		if ( !is_numeric($filter) ) return " AND 1 = 2 ";
 		return " AND ".$filter." IN (t.TargetState,t.SourceState) ";
 	}
}
