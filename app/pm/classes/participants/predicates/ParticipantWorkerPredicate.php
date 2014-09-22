<?php

class ParticipantWorkerPredicate extends FilterPredicate
{
 	function ParticipantWorkerPredicate()
 	{
 		parent::FilterPredicate('default');
 	}
 	
 	function _predicate( $filter )
 	{
		return " AND t.IsActive = 'Y' " .
			   " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r " .
			   "			  WHERE r.Participant = t.pm_ParticipantId" .
			   "			    AND r.Capacity > 0 ) ";
 	}
}
