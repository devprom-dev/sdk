<?php

class WikiPageTracesRevisionsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array (
			"( SELECT GROUP_CONCAT(
			            CONCAT_WS(':', tr.SourcePage,
			                (SELECT MIN(ch.WikiPageChangeId) FROM WikiPageChange ch
			                  WHERE tr.SourcePage = ch.WikiPage AND tr.RecordModified < ch.RecordCreated))
			          )
			     FROM WikiPageTrace tr
			    WHERE tr.TargetPage = ".$this->getPK($alias)."
			      AND tr.Baseline IS NULL
			      AND tr.IsActual = 'N') TracesRevisions "
		);
 	}
}
