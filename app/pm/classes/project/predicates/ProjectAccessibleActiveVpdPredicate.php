<?php

class ProjectAccessibleActiveVpdPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
		if ( !defined('PERMISSIONS_ENABLED') ) {
            return " AND ".$this->getAlias().".VPD IN (SELECT p.VPD FROM pm_Project p WHERE IFNULL(p.IsClosed,'N') = 'N') ";
        }

        $vpds = getSession()->getAccessibleVpds();
        if ( count($vpds) < 1 ) $vpds = array(0);

        if ( getFactory()->getObject('SharedObjectSet')->sharedInProject($this->getObject(), getSession()->getProjectIt()) ) {
            if ( getSession()->getProjectIt()->get('LinkedProject') != '' ) {
                $vpds = array_merge($vpds, $this->getObject()->getVpds());
            }
        }

        return " AND ".$this->getAlias().".VPD IN (SELECT p.VPD FROM pm_Project p WHERE p.VPD IN ('".join("','",$vpds)."') AND IFNULL(p.IsClosed,'N') = 'N') ";
 	}
}
