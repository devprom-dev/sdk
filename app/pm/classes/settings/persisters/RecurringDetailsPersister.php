<?php

class RecurringDetailsPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('RequestTemplates', 'TaskTemplates', 'AutoActions');
    }

    function getSelectColumns( $alias )
 	{
 	    return array(
 	        " (SELECT GROUP_CONCAT(CAST(s.cms_SnapshotId AS CHAR))
 	             FROM cms_Snapshot s 
 	            WHERE s.Recurring = t.pm_RecurringId
 	              AND s.ObjectClass = 'Request' ) RequestTemplates ",

            " (SELECT GROUP_CONCAT(CAST(s.cms_SnapshotId AS CHAR))
 	             FROM cms_Snapshot s 
 	            WHERE s.Recurring = t.pm_RecurringId
 	              AND s.ObjectClass = 'Task' ) TaskTemplates ",

            " (SELECT GROUP_CONCAT(CAST(s.pm_AutoActionId AS CHAR))
 	             FROM pm_AutoAction s 
 	            WHERE s.Recurring = t.pm_RecurringId 
 	              AND s.EventType = 6 ) AutoActions "
        );
 	}
}
 
