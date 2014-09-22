<?php

class TagKnowledgeBasePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		global $model_factory;
 		
 		$page = $model_factory->getObject('ProjectPage');
 		
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(p.WikiPageId as CHAR)) ".
 			"	  FROM WikiTag rt, WikiPage p " .
			"	 WHERE rt.Tag = " .$this->getPK($alias).
			"      AND rt.Wiki = p.WikiPageId ".
			"	   AND p.ReferenceName = '".$page->getReferenceName()."' ) KnowledgeBase " 
 		);
 	}
}
