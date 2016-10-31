<?php

class UserAccessPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'blocked':
 				return " AND EXISTS (SELECT 1 FROM cms_BlackList l WHERE l.SystemUser = t.cms_UserId) ";

			default:
				return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList l WHERE l.SystemUser = t.cms_UserId) ";
 		}
 	}
}
