<?php

class RequestOwnerPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('UserGroup');
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

