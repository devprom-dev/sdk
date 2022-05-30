<?php

class BaselineRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
	{
        $snapshot = getFactory()->getObject('cms_Snapshot');

	    $sqls = array(
            "SELECT IFNULL(t.Stage, t.Caption) pm_VersionId, t.Caption, t.VPD, 10 OrderNum
		 	   FROM cms_Snapshot t
		 	  WHERE (t.Type IS NOT NULL OR t.ObjectClass IN ('Requirement','TestScenario','HelpPage')) ".$snapshot->getVpdPredicate('t')
        );

	    if ( (!defined('PLAN_AS_BASELINE') || PLAN_AS_BASELINE == 'true') and class_exists('Issue') ) {
            $sqls[] =
                "SELECT t.Stage pm_VersionId, CONCAT(t.CaptionPrefix,t.CaptionType) Caption, t.VPD, 10 OrderNum
		 	       FROM ".getFactory()->getObject('Stage')->getRegistry()->getQueryClause($parms)." t
		 	      WHERE NOT EXISTS (SELECT 1 FROM cms_Snapshot s WHERE s.Stage = t.Stage)
		 	        AND ('".SystemDateTime::date('Y-m-d')."' <= IFNULL(t.FinishDate, NOW()) OR (t.UncompletedIssues + t.UncompletedTasks) > 0) ";

        }

	    if ( defined('ISSUE_AS_BASELINE') and class_exists('Issue') ) {
            $issue = getFactory()->getObject('Issue');
            $sqls[] =
                "SELECT IF(t.UID IS NULL, CONCAT('I-', t.pm_ChangeRequestId), t.UID), IF(t.UID IS NULL, CONCAT('I-', t.pm_ChangeRequestId, ' ', t.Caption),CONCAT(t.UID, ' ', t.Caption)), t.VPD, t.OrderNum 
                   FROM pm_ChangeRequest t 
                WHERE t.Type IS NULL AND t.FinishDate IS NULL ".$issue->getVpdPredicate('t');
        }

		return " ( " . join(' UNION ', $sqls) . " ORDER BY 2 ) ";
	}
}