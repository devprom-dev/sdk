<?php

class RequestTracePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$parts = preg_split('/:/', $filter);

 		if ( count($parts) < 2 ) return " AND 1 = 2 ";

		if ( $parts[1] == '' ) return " AND 1 = 2 ";
		
		if ( !class_exists(getFactory()->getClass($parts[0])) ) return " AND 1 = 2 ";
 		
		$object = getFactory()->getObject($parts[0]);
		
		if ( $object instanceof WikiPage )
		{
	 		$object_it = $object->getRegistry()->Query(
	 				array (
	 						new WikiRootTransitiveFilter($parts[1])
	 				)
	 		);
		}
		else
		{
	 		$object_it = $object->getExact(preg_split('/,/',$parts[1]));
		}
 		
 		if ( $object_it->count() > 0 )
 		{
			return " AND EXISTS (SELECT 1 FROM pm_ChangeRequestTrace r " .
				   "			  WHERE LCASE(r.ObjectClass) = '".strtolower(get_class($object_it->object))."'" .
				   " 			    AND r.ObjectId IN (".join(',',$object_it->idsToArray()).")" .
				   "	 			AND r.ChangeRequest = t.pm_ChangeRequestId) ";
 		}
 	}
} 
