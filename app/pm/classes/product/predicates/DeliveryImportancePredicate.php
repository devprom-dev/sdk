<?php

class DeliveryImportancePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$priority_it = getFactory()->getObject('Importance')->getRegistry()->Query(
 				array (
 						new FilterInPredicate(preg_split('/,/',$filter))
 				)
		);
 		 
 		if ( $priority_it->getId() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND IFNULL(t.Importance,0) IN (0,".join(",", $priority_it->idsToArray()).") ";
 	}
}
