<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

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

		$service = new WorkflowService($task_it->object);
		
		while ( !$task_it->end() )
		{
			$task_states = $task_it->object->getTerminalStates();
			
			if ( count($task_states) < 1 )
			{
				$task_states[] = 'resolved';
			}

			$service->moveToState($task_it, $task_states[0], $this->getDisplayName(),
					array (
							'LeftWork' => 0,
							'Assignee' => getSession()->getParticipantIt()->getId(),
							'Result' => getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ? text(1013) : ''
					)
			);
 			
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