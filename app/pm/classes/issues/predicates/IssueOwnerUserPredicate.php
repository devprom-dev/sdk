<?php

class IssueOwnerUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$user = getFactory()->getObject('cms_User');
 		
 		$ids = preg_split('/,/', $filter);
 		
 		$empty_value = in_array('none', $ids);

 		$ids = array_filter($ids, function( $value ) {
 		    return $value > 0;
 		});

 		if ( count($ids) > 0 )
 		{
     		$user_it = $user->getExact($ids);
     		
     		if ( $user_it->getId() < 1 ) return " AND 1 = 2 ";
     		
     		return " AND (t.Owner IN (".join(',',$user_it->idsToArray()).") ".($empty_value ? " OR t.Owner IS NULL " : "").")";
     		
 		}
 		else if ($empty_value)
 		{
 		    return " AND t.Owner IS NULL "; 
 		}
 		else
 		{
 		    return " AND 1 = 2 ";
 		}
 	}
}
