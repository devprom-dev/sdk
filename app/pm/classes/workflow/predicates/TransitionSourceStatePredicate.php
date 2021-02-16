<?php

class TransitionSourceStatePredicate extends FilterPredicate
{
 	function _predicate( $filter ) {
        $filter = \TextUtils::parseItems($filter);
 		return " AND EXISTS (SELECT 1 FROM pm_State s " .
 			   "			  WHERE s.pm_StateId = t.SourceState" .
 			   "				AND s.ReferenceName IN ('".join("','",$filter)."')) ";
 	}
}
