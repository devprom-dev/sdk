<?php

class RequestOwnerPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('UserGroup');
	}

    function modify($object_id, $parms)
    {
        if ( array_key_exists('UserGroup', $parms) ) {
            $registry = $this->getObject()->getRegistry();
            $requestIt = $registry->Query(
                array(
                    new FilterInPredicate($object_id),
                    new RequestOwnerPersister()
                )
            );
            if ( $requestIt->get('UserGroup') != $parms['UserGroup'] ) {
                $registry->Store($requestIt, array(
                    'Owner' => getFactory()->getObject('ProjectUser')->getRegistry()->Query(
                        array(
                            new ProjectUserGroupPredicate($parms['UserGroup'] != '' ? $parms['UserGroup'] : 'none')
                        )
                    )->getId()
                ));
            }
        }
        parent::modify($object_id, $parms);
    }

    function getSelectColumns( $alias )
 	{
		return array (
			" (SELECT GROUP_CONCAT(CAST(l.UserGroup AS CHAR)) 
			     FROM co_UserGroupLink l WHERE l.SystemUser = ".$alias.".Owner ) UserGroup "
		);
 	}
}

