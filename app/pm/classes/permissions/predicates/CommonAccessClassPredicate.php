<?php

class CommonAccessClassPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$class = $model_factory->getObject($filter);

		return " AND t.DisplayName = '".strtolower(get_class($class))."'";
 	}
}
