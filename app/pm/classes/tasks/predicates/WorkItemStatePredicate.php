<?php

class WorkItemStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		$states = array_filter(preg_split('/[,-]/', $filter), function($state) {
			return preg_match('/[A-Za-z0-9_]+/', $state);
		});
		if ( count($states) > 0 ) {
			return " AND ".$this->getAlias().".StateMeta IN ('".join($states,"','")."')";
		}
		else {
			return " AND 1 = 2 ";
		}
 	}
}