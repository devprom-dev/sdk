<?php

class ResourceUsageUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		switch ( $filter )
 		{
 			case '':
 			case 'all':
 				return "";
 				
 			default:
 				$group = $model_factory->getObject('UserGroup');
 				$group_it = $group->getExact($filter);

 				if ( $group_it->count() > 0 )
 				{
 					return " AND EXISTS (SELECT 1 FROM co_UserGroupLink l " .
 						   "			  WHERE l.SystemUser = p.SystemUser " .
 						   "				AND l.UserGroup = ".$group_it->getId().") ";
 				}
 		}
 	}
}
