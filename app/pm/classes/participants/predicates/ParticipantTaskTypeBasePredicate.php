<?php

class ParticipantTaskTypeBasePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_TaskType tt " .
			   "			  WHERE r.Participant = t.pm_ParticipantId" .
			   "				AND r.ProjectRole = tt.ProjectRole" .
			   "				AND tt.ParentTaskType = ".$filter." ) ";
 	}
}
