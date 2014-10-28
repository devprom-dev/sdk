<?php

include_once "BusinessAction.php";

class RequestBusinessActionResetTasks extends BusinessAction
{
 	function getId()
 	{
 		return '298637246';
 	}
	
	function apply( $object_it )
 	{
 		if ( $object_it->object->getAttributeType('OpenTasks') == '' ) return;
 		
		$task_it = $object_it->getRef('OpenTasks');
		
		$task_it->object->removeNotificator( 'EmailNotificator' );
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
		{
			while ( !$task_it->end() )
			{
				$task_it->modify( array('Release' => '') );
				$task_it->moveNext();
			}
		}

 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 	 	return !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ? text(1294) : text(1295);
 	}
}
