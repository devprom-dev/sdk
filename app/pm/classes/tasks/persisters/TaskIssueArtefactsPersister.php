<?php

class TaskIssueArtefactsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 			" ( SELECT GROUP_CONCAT(DISTINCT CONCAT_WS(':',l.ObjectClass,CAST(l.ObjectId AS CHAR))) " .
			"     FROM pm_ChangeRequestTrace l " .
			"    WHERE l.ChangeRequest = t.ChangeRequest ".
			"      AND l.ObjectClass NOT IN ('Task') ) IssueTraces ",

			" ( SELECT r.Description FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) IssueDescription ",
            " ( SELECT r.ClosedInVersion FROM pm_ChangeRequest r WHERE r.pm_ChangeRequestId = t.ChangeRequest) IssueVersion ",

			" (SELECT GROUP_CONCAT(CAST(a.pm_AttachmentId AS CHAR)) ".
			"    FROM pm_Attachment a ".
			"   WHERE a.ObjectId = t.ChangeRequest".
			"     AND a.ObjectClass = '".strtolower(get_class(getFactory()->getObject('Request')))."') IssueAttachment"
 		);
 	}
}

