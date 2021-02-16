<?php

class ProjectActiveVpdPredicate extends FilterPredicate
{
	function __construct() {
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter ) {
        return " AND ".$this->getAlias().".VPD IN (SELECT p.VPD FROM pm_Project p WHERE p.IsClosed = 'N') ";
 	}
}
