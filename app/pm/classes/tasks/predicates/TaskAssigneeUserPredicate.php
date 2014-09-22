<?php

class TaskAssigneeUserPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$null_value = false;
 		
 		$ids = preg_split('/,/', $filter);
 		
 		foreach( $ids as $key => $user_id )
 		{
 		    if ( in_array($user_id, array('', 'none')) )
 		    {
 		        unset($ids[$key]);
 		        
 		        $null_value = true;
 		    }
 		}
 		
 		if ( count($ids) < 1 )
 		{
 		    return $null_value ? " AND t.Assignee IS NULL " : " AND 1 = 2 ";
 		}
 		else
 		{
     		$user_it = $model_factory->getObject('cms_User')->getExact($ids);
     		
     		if ( $user_it->getId() < 1 ) return $null_value ? " AND t.Assignee IS NULL " : " AND 1 = 2 ";

     		return " AND ( ".($null_value ? "t.Assignee IS NULL OR " : "")." t.Assignee IN (SELECT p.pm_ParticipantId FROM pm_Participant p WHERE p.SystemUser IN (".join(',',$user_it->idsToArray())."))) ";
 		}
 	}
}
