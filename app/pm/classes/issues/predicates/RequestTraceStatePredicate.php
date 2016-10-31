<?php

class RequestTraceStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $states = preg_split('/,/', $filter);
		return " AND EXISTS (
		            SELECT 1 FROM pm_ChangeRequest r 
		             WHERE r.pm_ChangeRequestId = t.ChangeRequest AND r.State IN ('".join("','", $states)."')
		         )";
 	}
} 
