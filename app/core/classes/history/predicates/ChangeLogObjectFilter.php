<?php

class ChangeLogObjectFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$values = array();
 		foreach( preg_split('/,/', $filter) as $key => $class ) {
 			$class = getFactory()->getClass($class);
 			if ( !class_exists($class, false) ) {
				$values[] = $class;
			} else {
				$values[] = strtolower(get_class(getFactory()->getObject($class)));
			}
 		}
		return " AND t.ClassName IN ('".join($values,"','")."') ";
 	}
}