<?php

class TaskTypeUnifiedRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        $vpds = getSession()->getLinkedIt()->fieldToArray('VPD');
        if ( !getSession()->getProjectIt()->IsPortfolio() ) $vpds[] = getSession()->getProjectIt()->get('VPD');

        return "(
            SELECT DISTINCT
                   t.ReferenceName as entityId,
                   t.ReferenceName,
                   t.Caption,
                   '".$this->getObject()->getVpdValue()."' VPD,
                   0 OrderNum
              FROM pm_TaskType t
             WHERE t.VPD IN ('".join("','",$vpds)."')
             UNION
            SELECT 'z',
                   'z',
                   '".getFactory()->getObject('Task')->getDisplayName()."',
                   '".$this->getObject()->getVpdValue()."' VPD,
                   0
             ORDER BY 1
        )";
    }
}