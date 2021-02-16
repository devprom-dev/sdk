<?php

class FeatureStageFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $sqls = array();

 	    if ( $this->hasNone($filter) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM pm_ChangeRequest r, pm_Function u 
                                 WHERE r.Function = u.pm_FunctionId 
                                   AND u.ParentPath LIKE CONCAT('%,',t.pm_FunctionId,',%') 
                                   AND r.PlannedRelease IS NULL AND r.Iteration IS NULL) ";
        }

        if ( $this->hasAny($filter) ) {
            $sqls[] = " EXISTS (SELECT 1 FROM pm_ChangeRequest r, pm_Function u 
                                 WHERE r.Function = u.pm_FunctionId 
                                   AND u.ParentPath LIKE CONCAT('%,',t.pm_FunctionId,',%') 
                                   AND (r.PlannedRelease IS NOT NULL OR r.Iteration IS NOT NULL)) ";
        }

        $ids = \TextUtils::parseIds($filter);
        if ( count($ids) > 0 ) {
            $sqls[] = " EXISTS (SELECT 1 FROM pm_ChangeRequest r, pm_Function u 
                                 WHERE r.Function = u.pm_FunctionId 
                                   AND u.ParentPath LIKE CONCAT('%,',t.pm_FunctionId,',%') 
                                   AND (r.PlannedRelease IN (".join(',',$ids).") OR r.Iteration IN (".join(',',$ids)."))) ";
        }

 	    if ( count($sqls) < 1 ) return " AND 1 = 2 ";
 	    return " AND (".join(" OR ", $sqls).") ";
 	}
}
