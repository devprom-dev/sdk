<?php

class TaskTypeUnifiedRegistry extends ObjectRegistrySQL
{
    function getQueryClause(array $parms)
    {
        $vpds = getSession()->getLinkedIt()->fieldToArray('VPD');
        if ( !getSession()->getProjectIt()->IsPortfolio() ) $vpds[] = getSession()->getProjectIt()->get('VPD');

        return "(
            SELECT t.ReferenceName as pm_TaskTypeId,
                   t.ReferenceName,
                   MIN(t.Caption) Caption,
                   GROUP_CONCAT(DISTINCT t.ParentTaskType) ParentTaskType,
                   GROUP_CONCAT(DISTINCT t.pm_TaskTypeId) Ids,
                   '".$this->getObject()->getVpdValue()."' VPD,
                   MIN(t.OrderNum) OrderNum
              FROM pm_TaskType t
             WHERE t.VPD IN ('".join("','",$vpds)."')
             GROUP BY t.ReferenceName 
             UNION
            SELECT 'z',
                   'z',
                   '".getFactory()->getObject('Task')->getDisplayName()."',
                   0,
                   0,
                   '".$this->getObject()->getVpdValue()."' VPD,
                   0
             ORDER BY 3
        )";
    }
}