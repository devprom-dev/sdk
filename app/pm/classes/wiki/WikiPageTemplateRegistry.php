<?php

class WikiPageTemplateRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(), array ( new FilterBaseVpdPredicate() )
		);
	}
	
	function getQueryClause()
	{
	    return " (SELECT * FROM WikiPage WHERE ReferenceName = ".$this->getObject()->getReferenceName()." AND IsTemplate = 1 ) ";
	}
}