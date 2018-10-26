<?php
include_once SERVER_ROOT_PATH . "pm/classes/participants/predicates/ProjectUserGroupPredicate.php";

class RequestOwnerPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('UserGroup');
	}

	function map( &$parms )
    {
        if ( array_key_exists('UserGroup', $parms) ) {
            $userIt = getFactory()->getObject('ProjectUser')->getRegistry()->Query(
                array(
                    new ProjectUserGroupPredicate($parms['UserGroup'] != '' ? $parms['UserGroup'] : 'none')
                )
            );
            $parms['Owner'] = $userIt->getId();
        }
    }

	function getSelectColumns( $alias )
 	{
		return array (
			" IFNULL(
				(SELECT MIN(l.UserGroup) FROM co_UserGroupLink l, pm_Task s WHERE l.SystemUser = s.Assignee AND s.ChangeRequest = ".$alias.".pm_ChangeRequestId),
				(SELECT MIN(l.UserGroup) FROM co_UserGroupLink l WHERE l.SystemUser = ".$alias.".Owner)
			  ) UserGroup "
		);
 	}
}

