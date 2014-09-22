<?php

class ParticipantWorkloadPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
 				return " AND (SELECT SUM(r.Capacity) FROM pm_ParticipantRole r WHERE r.Participant = t.pm_ParticipantId) = 0 ";
 				
 			default:
 				return " AND (SELECT SUM(r.Capacity) FROM pm_ParticipantRole r WHERE r.Participant = t.pm_ParticipantId) > 0 ";
 		}
 	}
}
