<?php

class TaskTypeUnifiedRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        $ids = getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('pm_ProjectId');
        if ( !getSession()->getProjectIt()->IsPortfolio() ) $ids[] = getSession()->getProjectIt()->getId();

        $vpds = getFactory()->getObject('Project')->getRegistry()->Query(
            array (
                new FilterInPredicate($ids)
            )
        )->fieldToArray('VPD');

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