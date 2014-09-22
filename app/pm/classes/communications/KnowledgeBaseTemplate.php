<?php

include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageTemplate.php"; 
        
class KnowledgeBaseTemplate extends WikiPageTemplate
{
 	function getDisplayName()
 	{
 		return text(840);
 	}

	function getReferenceName() 
	{
		return WikiTypeRegistry::KnowledgeBase;
	}
 	
 	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'knowledgebase/templates?IsTemplate=1&';
	}
}