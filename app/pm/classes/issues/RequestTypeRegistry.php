<?php

class RequestTypeRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        $sqls = array(
            " SELECT t.pm_IssueTypeId,
                   t.ReferenceName,
                   t.Caption,
                   t.VPD,
                   t.OrderNum,
                   t.RecordModified,
                   t.RecordCreated
              FROM pm_IssueType t "
        );

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodology_it->get('IsRequirements') != ReqManagementModeRegistry::RDD ) {
            $sqls[] = "
                SELECT '',
                       NULL,
                       '".getFactory()->getObject('Request')->getDisplayName()."',
                       '".$this->getObject()->getVpdValue()."' VPD,
                       0,
                       NOW(),
                       NOW()
                 ORDER BY 1 ";
        }

        return "(".join("UNION", $sqls).")";
    }
}