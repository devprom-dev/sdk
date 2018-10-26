<?php

class TaskAssigneePersister extends ObjectSQLPersister
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
            $parms['Assignee'] = $userIt->getId();
        }
    }

 	function getSelectColumns( $alias )
 	{
 		return array(
 		    "(SELECT MIN(l.UserGroup) FROM co_UserGroupLink l WHERE l.SystemUser = ".$alias.".Assignee) UserGroup"
        );
 	}
}

