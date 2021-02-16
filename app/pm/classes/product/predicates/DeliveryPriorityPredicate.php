<?php

class DeliveryPriorityPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$priority_it = getFactory()->getObject('Priority')->getRegistry()->Query(
 				array (
 						new FilterInPredicate(preg_split('/,/',$filter))
 				)
		);
 		 
 		if ( $priority_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND IFNULL(t.Priority,0) IN (0,".join(",", $priority_it->idsToArray()).") ";
 	}
}
