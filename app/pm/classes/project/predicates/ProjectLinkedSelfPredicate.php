<?php

class ProjectLinkedSelfPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
        $ids = getSession()->getLinkedIt()->fieldToArray('pm_ProjectId');
        if ( !getSession()->getProjectIt()->IsPortfolio() ) $ids[] = getSession()->getProjectIt()->getId();

        if ( count($ids) < 1 ) $ids = array(0);
        return " AND ".$this->getAlias().".".$this->getObject()->getIdAttribute()." IN (".join(",",$ids).") ";
 	}
}
