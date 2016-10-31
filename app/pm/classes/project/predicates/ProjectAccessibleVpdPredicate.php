<?php

class ProjectAccessibleVpdPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
		if ( !defined('PERMISSIONS_ENABLED') ) return " AND 1 = 1 ";

        $vpds = getSession()->getAccessibleVpds();
        if ( count($vpds) < 1 ) $vpds = array(0);

        return " AND ".$this->getAlias().".VPD IN ('".join("','",$vpds)."') ";
 	}
}
