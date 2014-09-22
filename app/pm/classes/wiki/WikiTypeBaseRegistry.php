<?php

class WikiTypeBaseRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    if ( $this->getObject()->getReferenceName() == '' ) return parent::getQueryClause();

 	    return " (SELECT t.* FROM WikiPageType t WHERE t.PageReferenceName = '".strtolower($this->getObject()->getReferenceName())."' ) ";
 	}
}