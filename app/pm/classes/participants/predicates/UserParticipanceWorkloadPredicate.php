<?php

class UserParticipanceWorkloadPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'none':
 				return " AND (SELECT SUM(r.Capacity) FROM pm_ParticipantRole r, pm_Participant n ".
 				       "       WHERE r.Participant = n.pm_ParticipantId ".
 				       "         AND n.Project = ".getSession()->getProjectIt()->getId().
 				       "         AND n.SystemUser = t.cms_UserId) = 0 ";
 				
 			default:
 				return " AND (SELECT SUM(r.Capacity) FROM pm_ParticipantRole r, pm_Participant n ".
 				       "       WHERE r.Participant = n.pm_ParticipantId ".
 				       "         AND n.Project = ".getSession()->getProjectIt()->getId().
 				       "         AND n.SystemUser = t.cms_UserId) > 0 ";
 		}
 	}
}
