<?php

class ProjectAccessiblePredicate extends FilterPredicate
{
	function __construct()
	{
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
 		return    " AND t.pm_ProjectId IN ( ".
 				  "		SELECT pp.pm_ProjectId FROM pm_Project pp " .
				  "		 WHERE EXISTS ( SELECT 1 FROM pm_Participant r ".
				  "						 WHERE r.Project = pp.pm_ProjectId ".
				  "						   AND r.SystemUser = ".getSession()->getUserIt()->getId().
				  "						   AND IFNULL(r.IsActive, 'N') = 'Y' ) ".
 			      "		 UNION ALL ".
 			      "		SELECT i.pm_ProjectId FROM pm_Project i, pm_AccessRight r, pm_ProjectRole pr ".
 			      "	     WHERE pr.VPD = i.VPD ".
 				  "		   AND pr.ReferenceName = 'guest' ".
 				  "		   AND pr.pm_ProjectRoleId = r.ProjectRole ".
 				  "		   AND r.ReferenceName = 'pm_Project' ".
 				  "		   AND r.ReferenceType = 'Y' ".
 				  "		   AND r.AccessType IN ('view', 'modify') ".
 			      "	   )";
 	}
}
