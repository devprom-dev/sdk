<?php

class WikiPageFeatureTracesPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('FeatureIssues', 'FeatureRequirements');
	}

	function getSelectColumns( $alias )
 	{
 		return array (
			" (SELECT GROUP_CONCAT(r.Issues) 
                FROM pm_FunctionTrace r, WikiPage c, WikiPage cparent  
               WHERE c.WikiPageId = {$this->getPK($alias)}
                 AND c.ParentPath LIKE CONCAT(cparent.ParentPath, '%') 
                 AND cparent.WikiPageID = r.ObjectId 
                 AND r.IsActual = 'N' 
                 AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ) FeatureIssues ",

            " (SELECT GROUP_CONCAT(r.Requirements) 
                FROM pm_FunctionTrace r 
               WHERE r.ObjectId = {$this->getPK($alias)}
                 AND r.IsActual = 'N'
                 AND r.ObjectClass IN ('Requirement','TestScenario','HelpPage') ) FeatureRequirements "
		);
 	}
}
