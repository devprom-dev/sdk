<?php

class WikiPageFeatureTracesPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('FeatureIssues', 'FeatureRequirements');
	}

	function getSelectColumns( $alias )
 	{
 		return array (
			" (SELECT GROUP_CONCAT(r.Issues) ".
            "    FROM pm_FunctionTrace r, WikiPage c " .
            "   WHERE c.WikiPageId = ".$this->getPK($alias).
            "     AND FIND_IN_SET(r.ObjectId, c.ParentPath) > 0 ".
            "     AND r.IsActual = 'N' ".
            "     AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ) FeatureIssues ",

            " (SELECT GROUP_CONCAT(r.Requirements) ".
            "    FROM pm_FunctionTrace r " .
            "   WHERE r.ObjectId = ".$this->getPK($alias).
            "     AND r.IsActual = 'N' ".
            "     AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ) FeatureRequirements "
		);
 	}
}
