<?php

class ProjectParticipatePredicate extends FilterPredicate
{
	function __construct( $filter = '' )
	{
		parent::__construct( $filter != '' ? $filter : getSession()->getUserIt()->getId() );
	}
	
 	function _predicate( $filter )
 	{
 		if ( !defined('PERMISSIONS_ENABLED') ) return " AND 1 = 1 ";
 		
 		return    " AND t.pm_ProjectId IN ( ".
 				  "		SELECT r.Project FROM pm_Participant r, pm_ParticipantRole pr " .
				  "		 WHERE r.SystemUser IN (".$filter.") ".
                  "        AND r.pm_ParticipantId = pr.Participant ) ";
 	}
}
