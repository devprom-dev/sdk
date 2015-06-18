<?php

class ParticipantUserGroupPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case '':
 			case 'all':
 				return "";
 				
 			default:
 				$group_it = getFactory()->getObject('UserGroup')->getExact($filter);
 				if ( $group_it->count() > 0 ) {
 					return " AND EXISTS (SELECT 1 FROM co_UserGroupLink l " .
 						   "			  WHERE l.SystemUser = ".$this->getAlias().".SystemUser " .
 						   "				AND l.UserGroup = ".$group_it->getId().") ";
 				}
 				else {
 					return " AND 1 = 2 ";
 				}
 		}
 	}
}
