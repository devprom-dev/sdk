<?php

class UserWorkerPredicate extends FilterPredicate
{
 	function __construct() {
        $ids = array_filter(
            array_merge(
                array(getSession()->getProjectIt()->getId()),
                preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject'))
            ),
            function($value) {
                return $value > 0;
            }
        );
        if ( count($ids) < 1 ) $ids = array(0);
        parent::__construct($ids);
 	}

 	function _predicate( $filter )
 	{
        $ids = \TextUtils::parseIds($filter);
        if ( count($ids) < 1 ) return " AND 1 = 2 ";

        $andSqls = array();
        $orSqls = array();

 		if ( defined('PERMISSIONS_ENABLED') ) {
            $andSqls[] =
                " EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_Participant pt " .
                "     	   WHERE r.Participant = pt.pm_ParticipantId" .
                "			 AND pt.IsActive = 'Y' ".
                "			 AND pt.SystemUser = t.cms_UserId ".
                "			 AND pt.Project IN (".join(',',$ids).") ) ";

            $orSqls[] = join(' AND ', $andSqls);
 		}

        $orSqls[] = " EXISTS (SELECT 1 FROM pm_Task s, pm_Project p 
                               WHERE s.VPD = p.VPD
                                 AND p.pm_ProjectId IN (".join(',',$ids).") 
                                 AND s.Assignee = t.cms_UserId) ";

        $orSqls[] = " EXISTS (SELECT 1 FROM pm_ChangeRequest s, pm_Project p 
                               WHERE s.VPD = p.VPD
                                 AND p.pm_ProjectId IN (".join(',',$ids).") 
                                 AND s.Owner = t.cms_UserId) ";

		return " AND NOT EXISTS (SELECT 1 FROM cms_BlackList bl 
		                          WHERE bl.SystemUser = t.cms_UserId) 
		         AND (" . join(" OR ", $orSqls) . ")";
 	}
}
