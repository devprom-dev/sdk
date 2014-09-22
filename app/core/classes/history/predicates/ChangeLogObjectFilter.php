<?php

class ChangeLogObjectFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$values = preg_split('/,/', $filter);
 		
 		foreach( $values as $key => $class )
 		{
 			$class = getFactory()->getClass($class);
 			
 			if ( !class_exists($class, false) ) continue;
 			
 		    $values[$key] = strtolower(get_class(getFactory()->getObject($class)));
 		}
 		
		return " AND t.ClassName IN ('".join($values,"','")."') ";
 	}
}