<?php

class UserStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
			case 'active':
				return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList l WHERE l.SystemUser = t.cms_UserId) AND IFNULL(t.IsReadonly, 'Y') <> 'Y' ";

			case 'nonblocked':
				return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList l WHERE l.SystemUser = t.cms_UserId) ";

 			case 'blocked':
 				return " AND EXISTS (SELECT 1 FROM cms_BlackList l WHERE l.SystemUser = t.cms_UserId) ";
 		}
 	}
}
