<?php

class FilterNotInPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = array_filter(preg_split('/,/', $filter), function( $value ) 
 		{
 		    return $value > 0;
 		});

 		if ( count($ids) > 0 )
 		{
 		    $object = $this->getObject();
 		    
   		    return " AND t.".$object->getClassName()."Id NOT IN (".join(',',$ids).") ";
 		}
 		
 		return " AND 1 = 2 ";
 	}
}
