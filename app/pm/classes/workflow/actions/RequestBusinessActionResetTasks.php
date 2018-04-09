<?php
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionResetTasks extends BusinessActionWorkflow
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
				$task_it->object->modify_parms($task_it->getId(), array('Release' => ''));
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
