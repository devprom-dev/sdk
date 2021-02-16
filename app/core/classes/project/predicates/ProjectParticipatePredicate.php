<?php

class ProjectParticipatePredicate extends FilterPredicate
{
	function __construct( $filter = '' ) {
		parent::__construct( $filter != '' ? $filter : getSession()->getUserIt()->getId() );
	}
	
 	function _predicate( $filter )
 	{
 		if ( !defined('PERMISSIONS_ENABLED') ) return " AND 1 = 1 ";

 		$sqls = array(
            " t.pm_ProjectId IN ( ".
            "		SELECT r.Project FROM pm_Participant r, pm_ParticipantRole pr " .
            "		 WHERE r.SystemUser IN (".$filter.") ".
            "          AND r.pm_ParticipantId = pr.Participant ) ",

            " EXISTS (
                SELECT 1 FROM pm_AccessRight ar, pm_ProjectRole pr
                 WHERE pr.VPD = t.VPD
                   AND pr.ReferenceName = 'guest'
                   AND ar.ProjectRole = pr.pm_ProjectRoleId
                   AND ar.VPD = t.VPD
                   AND ar.ReferenceName = 'project'
                   AND ar.ReferenceType = 'Y'
                   AND ar.AccessType IN ('view')
                ) "
        );

 		return " AND (" . join(" OR ", $sqls) . ") ";
 	}
}
