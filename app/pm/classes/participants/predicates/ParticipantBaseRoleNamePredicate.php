<?php

class ParticipantBaseRoleNamePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_ProjectRole prr " .
			   "			  WHERE r.Participant = t.pm_ParticipantId" .
			   "				AND r.ProjectRole = prr.pm_ProjectRoleId" .
			   "				AND prr.ReferenceName = '".$filter."' ) ";
 	}
}
