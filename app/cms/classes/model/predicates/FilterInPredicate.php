<?php

class FilterInPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$filter = !is_array($filter) ? preg_split('/,/', $filter) : $filter;
 		
 		$ids = array_filter($filter, function( $value ) 
 		{
 		    return is_numeric($value) && $value != '';
 		});

 		if ( count($ids) > 0 )
 		{
	 		array_walk($ids, function( &$value, $key ) 
	 		{
	 			$value = is_numeric($value) ? $value : '"'.$value.'"';
	 		});
 			
 		    $object = $this->getObject();
 		    
   		    return " AND t.".$object->getEntityRefName()."Id IN (".join(',',$ids).") ";
 		}
 		else
 		{
 			throw new Exception('Empty object ('.get_class($this->getObject()).') identifier');
 		}
 	}
}
