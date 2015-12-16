<?php

class WikiPageBranchesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
			"( SELECT GROUP_CONCAT(CONCAT_WS(':',p.DocumentId,tr.TargetPage))
			     FROM WikiPageTrace tr, WikiPage p
			    WHERE tr.SourcePage = ".$this->getPK($alias)."
			      AND tr.Type = 'branch'
			      AND tr.TargetPage = p.WikiPageId) TargetBranches",

			"( SELECT GROUP_CONCAT(CONCAT_WS(':',p.DocumentId,tr.SourcePage))
			     FROM WikiPageTrace tr, WikiPage p
			    WHERE tr.TargetPage = ".$this->getPK($alias)."
			      AND tr.Type = 'branch'
			      AND tr.SourcePage = p.WikiPageId) SourceBranches"
		);
 	}
}
