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
			      AND LOCATE(CONCAT('".$this->getObject()->getEntityRefName().":',".$this->getPK($alias)."),ub.Dependency)) ProjectPage "
		);
 	}
}
