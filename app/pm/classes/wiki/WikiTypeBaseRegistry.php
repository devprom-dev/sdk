<?php

class WikiTypeBaseRegistry extends ObjectRegistrySQL
{
 	function getQueryClause(array $parms)
 	{
 	    if ( $this->getObject()->getReferenceName() == '' ) return parent::getQueryClause($parms);

 	    return " (SELECT t.* FROM WikiPageType t WHERE t.PageReferenceName = '".strtolower($this->getObject()->getReferenceName())."' ) ";
 	}
}