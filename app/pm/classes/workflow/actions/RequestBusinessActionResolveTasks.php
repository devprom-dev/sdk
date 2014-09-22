<?php

include_once "BusinessAction.php";

class RequestBusinessActionResolveTasks extends BusinessAction
{
 	function getId()
 	{
 		return '2016503085';
 	}
	
	function apply( $object_it )
 	{
		$task_it = $object_it->getRef('OpenTasks');
		
		$task_it->object->removeNotificator( 'EmailNotificator' );
				
		while ( !$task_it->end() )
		{
			$task_states = $task_it->object->getTerminalStates();
			
			if ( count($task_states) < 1 )
			{
				$task_states[] = 'resolved';
			}

			$task_parms = array( 
				'State' => $task_states[0],
				'LeftWork' => 0,
				'Assignee' => getSession()->getParticipantIt()->getId() 
			);
 				 
			if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
			{
				$task_parms['Result'] = text(1013);
			}
 				
 			$task_it->modify( $task_parms );
 			
 			$task_it->moveNext();
 		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1242);
 	}
}