<?php

class TaskFeaturePersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Feature');
    }

    function getSelectColumns( $alias )
 	{
 		return array(
            " CONCAT_WS(',', 
                ( SELECT r.Function FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest),
                ( SELECT GROUP_CONCAT(DISTINCT CAST(ft.Feature AS CHAR)) FROM pm_FunctionTrace ft, pm_TaskTrace tt 
                   WHERE tt.Task = t.pm_TaskId AND tt.ObjectClass = 'Requirement'
                     AND tt.ObjectId = ft.ObjectId AND ft.ObjectClass = 'Requirement' 
                     AND NOT EXISTS (SELECT 1 FROM pm_ChangeRequest r WHERE r.Function = ft.Feature AND r.pm_ChangeRequestId = t.ChangeRequest) )
                ) Feature "
 		);
 	}
}

