<?php

class TaskFeaturePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $sqls = array();

 	    if ( strpos($filter, 'none') !== false ) {
            $sqls[] =
                " (EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function IS NULL AND r.pm_ChangeRequestId = t.ChangeRequest) OR t.ChangeRequest IS NULL) ".
                " AND NOT EXISTS (SELECT 1 FROM pm_FunctionTrace ft, pm_TaskTrace tt 
                                   WHERE tt.Task = t.pm_TaskId AND tt.ObjectClass = 'Requirement' 
                                     AND tt.ObjectId = ft.ObjectId AND ft.ObjectClass = 'Requirement') ";
        }

        if ( strpos($filter, 'any') !== false ) {
            $sqls[] =
                " EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function IS NOT NULL AND r.pm_ChangeRequestId = t.ChangeRequest) ".
                " AND EXISTS (SELECT 1 FROM pm_FunctionTrace ft, pm_TaskTrace tt 
                               WHERE tt.ObjectClass = 'Requirement' AND tt.ObjectId = ft.ObjectId AND ft.ObjectClass = 'Requirement') ";
        }

		$ids = \TextUtils::parseIds($filter);
		if ( count($ids) > 0 ) {
            $sqls[] =
                " EXISTS (SELECT 1 FROM pm_ChangeRequest r 
                           WHERE r.Function IN (".join(',',$ids).") AND r.pm_ChangeRequestId = t.ChangeRequest) ";

            $sqls[] =
                " EXISTS (SELECT 1 FROM pm_FunctionTrace ft, pm_TaskTrace tt 
                           WHERE tt.Task = t.pm_TaskId AND tt.ObjectClass = 'Requirement' AND ft.Feature IN (".join(',',$ids).")
                             AND tt.ObjectId = ft.ObjectId AND ft.ObjectClass = 'Requirement') ";
        }

		return count($sqls) < 1 ? " AND 1 = 2 " : " AND (".join(" OR ", $sqls).") ";
 	}
}
