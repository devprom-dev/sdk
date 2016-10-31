<?php

class WikiPageHistoryPersister extends ObjectSQLPersister
{
	private $sinceDate = '';

	function setSinceDate( $date ) {
		$mapper = new ModelDataTypeMappingDate();
		$this->sinceDate = $mapper->map(DAL::Instance()->Escape($date));
	}

	function getSelectColumns( $alias )
	{
		return array (
			"IFNULL(
				(SELECT IFNULL(c.Content,' ') FROM WikiPageChange c
			   	  WHERE c.WikiPage = ".$this->getPK($alias)."
			     	AND DATE(c.RecordCreated) > '".$this->sinceDate."'
			      ORDER BY WikiPageChangeId ASC LIMIT 1),
			    (SELECT c.Content FROM WikiPage c
			      WHERE c.WikiPageId = ".$this->getPK($alias).")) Content",
			"(SELECT c.WikiPageChangeId FROM WikiPageChange c
			   	  WHERE c.WikiPage = ".$this->getPK($alias)."
			     	AND DATE(c.RecordCreated) > '".$this->sinceDate."'
			      ORDER BY WikiPageChangeId ASC LIMIT 1) RevisionId "
		);
	}
}
