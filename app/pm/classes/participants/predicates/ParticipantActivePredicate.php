<?php

class ParticipantActivePredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
		return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.SystemUser)
		         AND EXISTS (SELECT 1 FROM pm_Project p WHERE p.pm_ProjectId = t.Project AND p.IsClosed = 'N') ";
 	}
}
