<?php

class TaskAssigneePersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
 		    "(SELECT MIN(l.UserGroup) FROM co_UserGroupLink l WHERE l.SystemUser = ".$alias.".Assignee) UserGroup"
        );
 	}
}

