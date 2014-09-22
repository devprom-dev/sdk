<?php

class KnowledgeBaseDocumentList extends PMWikiDocumentList
{
 	function getSorts()
	{
	    return array_merge( array(new NativeProjectSortClause($this->getObject()->getVpdValue())), parent::getSorts() );
	}
	
	function IsNeedToSelect()
	{
	    return false;
	}
} 