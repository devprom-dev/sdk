<?php

class CommentEntityPredicate extends FilterPredicate 
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$object = $model_factory->getObject($filter);
 		if ( $object->getClassName() != '' )
 		{
 			return " AND t.ObjectClass = '".strtolower($filter)."'";
 		}
 	}
} 
