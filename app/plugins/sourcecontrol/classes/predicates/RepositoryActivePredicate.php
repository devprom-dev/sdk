<?php

class RepositoryActivePredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
		return " AND EXISTS ( SELECT 1 FROM pm_Project p WHERE p.VPD = t.VPD AND IFNULL(p.IsClosed,'N') = 'N' ) ";
 	}
}
