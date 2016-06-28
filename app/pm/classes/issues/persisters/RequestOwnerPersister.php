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

	function modify( $object_id, $parms )
	{
		if ( array_key_exists('Owner', $parms) && $this->getObject()->getAttributeType('OpenTasks') != '' ) {
			$task_it = $this->getObject()->getExact($object_id)->getRef('OpenTasks');
			while ( !$task_it->end() )
			{
				$task_it->object->modify_parms(
					$task_it->getId(),
					array('Assignee' => $parms['Owner'])
				);
				$task_it->moveNext();
			}
		}
	}
}

