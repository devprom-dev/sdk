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
 				  "		SELECT r.Project FROM pm_Participant r " .
				  "		 WHERE r.SystemUser IN (".$filter.") ".
 			      "		 UNION ALL ".
 			      "		SELECT i.pm_ProjectId FROM pm_Project i, pm_AccessRight r, pm_ProjectRole pr ".
 			      "	     WHERE pr.VPD = i.VPD ".
 				  "		   AND pr.ReferenceName = 'guest' ".
 				  "		   AND pr.pm_ProjectRoleId = r.ProjectRole ".
 				  "		   AND r.ReferenceName = 'pm_Project' ".
 				  "		   AND r.ReferenceType = 'Y' ".
 				  "		   AND r.AccessType IN ('view', 'modify') ".
 			      "	   ) ";
 	}
}
