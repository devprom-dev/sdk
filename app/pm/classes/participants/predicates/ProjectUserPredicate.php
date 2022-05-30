<?php

class ProjectUserPredicate extends FilterPredicate
{
 	function __construct() {
 		parent::__construct(getSession()->getProjectIt()->getId());
 	}

 	function _predicate( $filter )
 	{
        $ids = \TextUtils::parseIds($filter);
        if ( count($ids) < 1 ) return " AND 1 = 2 ";

        $andSqls[] = " NOT EXISTS (SELECT 1 FROM cms_BlackList bl WHERE bl.SystemUser = t.cms_UserId) ";

 		if ( defined('PERMISSIONS_ENABLED') ) {
            $andSqls[] =
                " EXISTS (SELECT 1 FROM pm_ParticipantRole r, pm_Participant pt " .
                "     	   WHERE r.Participant = pt.pm_ParticipantId" .
                "			 AND pt.IsActive = 'Y' ".
                "			 AND pt.SystemUser = t.cms_UserId ".
                "			 AND pt.Project IN (".join(',',$ids).") ) ";
 		}

		return " AND " . join(' AND ', $andSqls);
 	}
}
