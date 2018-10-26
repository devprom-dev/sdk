<?php

class BaselineRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
        $snapshot = getFactory()->getObject('cms_Snapshot');

	    $sqls = array(
	        "SELECT t.Stage pm_VersionId, CONCAT(t.CaptionPrefix,t.CaptionType) Caption, t.VPD, 0 OrderNum, t.Version, t.Release
		 	   FROM ".getFactory()->getObject('Stage')->getRegistry()->getQueryClause()." t",

            "SELECT t.Caption, t.Caption, t.VPD, 10, NULL, NULL
		 	   FROM cms_Snapshot t
		 	  WHERE t.Type = 'branch' ".$snapshot->getVpdPredicate('t')." AND t.Stage IS NULL"
        );

	    if ( defined('ISSUE_AS_BASELINE') ) {
            $sqls[] =
                "SELECT CONCAT('I-', t.pm_ChangeRequestId), CONCAT('I-', t.pm_ChangeRequestId, ' ', t.Caption), t.VPD, t.OrderNum, NULL, NULL 
                   FROM pm_ChangeRequest t WHERE t.Type IS NULL AND t.FinishDate IS NULL ";
        }

		return " ( " . join(' UNION ', $sqls) . " ) ";
	}
}