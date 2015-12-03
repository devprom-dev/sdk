<?php

class FilterInPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$filter = !is_array($filter) ? preg_split('/,/', $filter) : $filter;
 		
 		$ids = array_filter($filter, function( $value ) {
 		    return $value != '';
 		});
 		if ( count($ids) > 0 )
 		{
	 		array_walk($ids, function( &$value, $key ) {
	 			$value = is_numeric($value) ? $value : '"'.$value.'"';
	 		});
   		    return " AND t.".$this->getObject()->getEntityRefName()."Id IN (".join(',',$ids).") ";
 		}
 		else
 		{
 			return " AND 1 = 2 ";
 		}
 	}
}
