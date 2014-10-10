<?php

class ParticipantWorkerPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
		return " AND t.IsActive = 'Y' " .
			   " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.SystemUser) ".
			   " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r " .
			   "			  WHERE r.Participant = t.pm_ParticipantId" .
			   "			    AND r.Capacity > 0 ) ";
 	}
}
