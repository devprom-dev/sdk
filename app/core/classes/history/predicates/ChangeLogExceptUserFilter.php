<?php

class ChangeLogExceptUserFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$user = $model_factory->getObject('cms_User');
 		$user_it = $user->getExact( $filter );
 		
 		if ( $user_it->count() < 1 ) return " AND 1 = 2 ";
 		
 		return " AND IFNULL(t.SystemUser, 0) <> ".$filter;
 	}
}
