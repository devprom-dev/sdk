<?php

class FunctionTraceObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		if ( !is_a($filter, 'OrderedIterator') )
 		{
	 		$parts = preg_split('/,/', $filter);
	 		
	 		if ( count($parts) < 2 ) return " AND 1 = 2 ";
	 		
	 		$object_it = $model_factory->getObject($parts[0])->getExact(array_slice($parts, 1));
 		}
 		else
 		{
 			$object_it = $filter;
 		}
 		
 		if ( $object_it->count() < 1 ) return " AND 1 = 2 ";

		return " AND t.ObjectClass = '".strtolower(get_class($object_it->object))."'" .
			   " AND t.ObjectId IN (".join(",", $object_it->idsToArray()).")";
 	}
}
