<?php

class RequestTypeRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        $hasIncrements = getSession()->IsRDD();

        $sqls = array(
            " SELECT t.pm_IssueTypeId,
                   t.ReferenceName,
                   t.Caption,
                   t.VPD,
                   t.OrderNum,
                   t.RelatedColor,
                   t.RecordModified,
                   t.RecordCreated,
                   t.RecordVersion,
                   t.Option1,
                   '".($hasIncrements ? 'Increment' : 'Request')."' IssueClassName
              FROM pm_IssueType t "
        );

        if ( !$hasIncrements ) {
            $sqls[] = "
                SELECT '',
                       NULL,
                       '".getFactory()->getObject('Request')->getDisplayName()."',
                       '".$this->getObject()->getVpdValue()."' VPD,
                       10,
                       '',
                       NOW(),
                       NOW(),
                       0,
                       'N',
                       'Request'
                 ORDER BY 1 ";
        }

        return "(".join("UNION", $sqls).")";
    }
}