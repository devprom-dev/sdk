<?php

class TagKnowledgeBasePersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('KnowledgeBase');
    }

 	function getSelectColumns( $alias )
 	{
 		return array(
 			" ( SELECT GROUP_CONCAT(DISTINCT CAST(p.WikiPageId as CHAR)) ".
 			"	  FROM WikiTag rt, WikiPage p " .
			"	 WHERE rt.Tag = " .$this->getPK($alias).
			"      AND rt.Wiki = p.WikiPageId ".
			"	   AND p.ReferenceName = '".getFactory()->getObject('ProjectPage')->getReferenceName()."' ) KnowledgeBase "
 		);
 	}
}
