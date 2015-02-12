<?php

class UserWorkerPredicate extends FilterPredicate
{
 	function __construct()
 	{
 		parent::__construct('default');
 	}
 	
 	function _predicate( $filter )
 	{
		return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.cms_UserId) ".
			   " AND EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_Participant pt " .
			   "			  WHERE r.Participant = pt.pm_ParticipantId" .
			   "			    AND pt.IsActive = 'Y' ".
			   "			    AND pt.SystemUser = t.cms_UserId ".
			   "				AND pt.Project = ".getSession()->getProjectIt()->getId().
			   "			    AND r.Capacity > 0 ) ";
 	}
}
