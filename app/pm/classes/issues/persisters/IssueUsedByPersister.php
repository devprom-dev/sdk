<?php

class IssueUsedByPersister extends ObjectSQLPersister
{
	function getAttributes()
	{
		return array('ProjectPage');
	}

	function getSelectColumns( $alias )
 	{
 		return array(
			" (SELECT GROUP_CONCAT(CAST(ub.WikiPageId AS CHAR)) 
			     FROM WikiPage ub 
			    WHERE ub.ReferenceName = ".WikiTypeRegistry::KnowledgeBase."
			      AND ub.Dependency IS NOT NULL
			      AND FIND_IN_SET(CONCAT('".get_class($this->getObject()).":',".$this->getPK($alias)."),ub.Dependency)) ProjectPage "
		);
 	}
}
