<?php

class DeliveryStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
	 	$metastate = getFactory()->getObject('StateMeta');
		$metastate->setAggregatedStateObject(getFactory()->getObject('IssueState'));
	 	$metastate->setStatesDelimiter("-");
	 	
		$states = $metastate->getRegistry()->Query(array())->fieldToArray('ReferenceName');
 		
		$filter = array_filter( preg_split('/,/', $filter), function($value) use ($states) {
					return in_array($value, $states);
		});
		if ( count($filter) < 1 ) return " AND 1 = 2 ";
		
		$items = array();
		foreach( $filter as $item )
		{
			$items = array_merge($items, preg_split('/-/', $item));  
		}

 		return " AND IFNULL(t.State,'dummy') IN ('dummy','".join("','", $items)."') ";
 	}
}
