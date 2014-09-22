<?php

class ParticipantUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
		$user = $model_factory->getObject('cms_User');
		
		$user_it = $user->getExact($filter);

		if ( $user_it->count() > 0 )
		{
			return " AND t.SystemUser = ".$user_it->getId()." ";
		}
 	}
}
