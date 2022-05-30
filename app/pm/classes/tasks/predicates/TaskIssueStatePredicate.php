<?php

class TaskIssueStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $states = array_filter(
 	        preg_split('/,/', $filter),
            function( $value ) {
 	            return preg_match('/[A-Za-z0-9_]+/', $value);
            }
        );
        $taskTypeSql = '';
        if ( count($states) < 1 ) {
            $states = array(0);
        }
        else {
            $typeDefault = getFactory()->getObject('pm_TaskType')->getRegistry()->QueryKeys(
                    array (
                        new FilterBaseVpdPredicate(),
                        new TaskTypeStateRelatedPredicate($states)
                    )
                )->idsToArray();
            if ( count($typeDefault) > 0 ) {
                $taskTypeSql = " AND t.TaskType IN (".join(',',$typeDefault).") ";
            }
        }
        return " AND ( 
                    t.ChangeRequest IS NULL ".$taskTypeSql." OR 
                    EXISTS (
                        SELECT 1 FROM pm_ChangeRequest r 
                         WHERE r.pm_ChangeRequestId = t.ChangeRequest 
                           AND r.State IN ('".join("','", $states)."'))
                 ) ";
 	}
}
