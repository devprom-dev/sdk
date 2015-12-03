<?php

class RequestTypeUnifiedRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        return "(
            SELECT DISTINCT
                   t.ReferenceName as entityId,
                   t.ReferenceName,
                   t.Caption,
                   '".$this->getObject()->getVpdValue()."' VPD,
                   0 OrderNum
              FROM pm_IssueType t
             WHERE t.VPD IN ('".join("','",getFactory()->getObject('RequestType')->getVpds())."')
             UNION
            SELECT 'z',
                   'z',
                   '".getFactory()->getObject('Request')->getDisplayName()."',
                   '".$this->getObject()->getVpdValue()."' VPD,
                   0
             ORDER BY 1
        )";
    }
}