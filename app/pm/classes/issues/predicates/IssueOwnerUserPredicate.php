<?php

class IssueOwnerUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$ids = preg_split('/,/', $filter);
 		$empty_value = in_array('none', $ids);

 		$ids = array_filter($ids, function( $value ) {
 		    return $value > 0;
 		});

		$sqls = array();
 		if ( count($ids) > 0 ) {
     		$user_it = getFactory()->getObject('cms_User')->getExact($ids);
     		if ( $user_it->getId() < 1 ) return " AND 1 = 2 ";

			$sqls[] = " (t.Owner IN (".join(',',$user_it->idsToArray()).") ".($empty_value ? " OR t.Owner IS NULL " : "").")";
			$sqls[] = " EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = t.pm_ChangeRequestId AND s.Assignee IN (".join(',',$user_it->idsToArray()).") ".($empty_value ? " OR s.Assignee IS NULL " : "").") ";
 		}
 		else if ($empty_value)
 		{
			$sqls[] = " t.Owner IS NULL AND NOT EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = t.pm_ChangeRequestId) ";
			$sqls[] = " EXISTS (SELECT 1 FROM pm_Task s WHERE s.ChangeRequest = t.pm_ChangeRequestId AND s.Assignee IS NULL) ";
 		}
 		else
 		{
 		    return " AND 1 = 2 ";
 		}
		return " AND (".join(' OR ', $sqls).")";
 	}
}
