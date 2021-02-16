<?php

class DeliveryProductTypePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$types = 
 			array_intersect(
 					getFactory()->getObject('DeliveryProduct')->getRegistry()->Query()->fieldToArray('ReferenceName'),
 					preg_split('/,/',$filter)
 			);
 		
 		if ( count($types) < 1 ) return " AND 1 = 2 ";
 		
 		return " AND ObjectType IN ('".join("','", $types)."') ";
 	}
}
