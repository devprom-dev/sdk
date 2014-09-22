<?php

class QuestionAuthorFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		switch ( $filter )
 		{
 			case 'all':
 				return "";

 			default:
 				$user = $model_factory->getObject('cms_User');
 				$user_it = $user->getExact($filter);
 				
 				if ( $user_it->count() > 0 )
 				{
 					return " AND t.Author = ".$user_it->getId();
 				}
 				else
 				{
 					return " AND 1 = 2 ";
 				}
 		}
 	}
}
