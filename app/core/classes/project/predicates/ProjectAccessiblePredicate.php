<?php

class ProjectAccessiblePredicate extends FilterPredicate
{
    private $user_it = null;

	function __construct( $user_it ) {
	    $this->user_it = $user_it;
		parent::__construct('dummy');
	}
	
 	function _predicate( $filter )
 	{
		if ( !defined('PERMISSIONS_ENABLED') ) return " AND 1 = 1 ";

		$user_id = $this->user_it->getId();
		if ( $user_id == '' ) $user_id = 0;

		$accessPolicy = new CoAccessPolicy(getFactory()->getCacheService(), 'apps/'.$this->user_it->getId());
        $allProjectsModuleIt = getFactory()->getObject('Module')->getExact('ee/allprojects');
        if ( $accessPolicy->can_read($allProjectsModuleIt) ) return " AND 1 = 1 ";

        return  " AND t.pm_ProjectId IN ( ".
                "		SELECT r.Project ".
                "       FROM pm_Participant r ".
                "      WHERE r.SystemUser = ".$user_id.
                "		 UNION ALL ".
                "		SELECT pl.Source FROM pm_ProjectLink pl " .
                "		 WHERE EXISTS ( SELECT 1 FROM pm_Participant r ".
                "						 WHERE r.Project = pl.Target ".
                "						   AND r.SystemUser = ".$user_id." ) ".
                "		 UNION ALL ".
                "		SELECT pl.Target FROM pm_ProjectLink pl " .
                "		 WHERE EXISTS ( SELECT 1 FROM pm_Participant r ".
                "						 WHERE r.Project = pl.Source ".
                "						   AND r.SystemUser = ".$user_id." ) ".
                "		 UNION ALL ".
                "		SELECT i.pm_ProjectId FROM pm_Project i, pm_AccessRight r, pm_ProjectRole pr ".
                "	     WHERE pr.VPD = i.VPD ".
                "		   AND pr.ReferenceName = 'guest' ".
                "		   AND pr.pm_ProjectRoleId = r.ProjectRole ".
                "		   AND r.ReferenceName IN ('pm_Project','project') ".
                "		   AND r.ReferenceType = 'Y' ".
                "		   AND r.AccessType IN ('view', 'modify') ".
                "	   )";
 	}
}
