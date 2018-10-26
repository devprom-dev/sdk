<?php

class ProjectUserGroupPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    $ids = TextUtils::parseIds($filter);

 	    if ( count($ids) < 1 || in_array('none', $ids) ) {
            return " AND NOT EXISTS (SELECT 1 FROM co_UserGroupLink l WHERE l.SystemUser = t.cms_UserId ) ";
        }
        else {
            return
                " AND EXISTS (SELECT 1 FROM co_UserGroupLink l " .
                "			  WHERE l.SystemUser = t.cms_UserId " .
                "				AND l.UserGroup IN (".join(',',$ids).")) ";
        }
 	}
}
