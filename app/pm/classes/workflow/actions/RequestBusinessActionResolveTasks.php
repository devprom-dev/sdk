<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionResolveTasks extends BusinessActionWorkflow
{
 	function getId() {
 		return '2016503085';
 	}
	
	function apply( $object_it )
 	{
 		if ( $object_it->object->getAttributeType('OpenTasks') == '' ) return;
 				
		$task_it = $object_it->getRef('OpenTasks');
		$task_it->object->removeNotificator( 'EmailNotificator' );

		$service = new WorkflowService($task_it->object);
		
		while ( !$task_it->end() )
		{
			$task_state = array_shift($task_it->object->getTerminalStates());
			if ( $task_state == '' ) {
				$task_state = array_pop($task_it->object->getStates());
			}

			$parms = array (
				'LeftWork' => 0
			);
			if ( $task_it->get('Assignee') == '' ) {
				$parms['Assignee'] = getSession()->getUserIt()->getId();
			}
			$service->moveToState(
				$task_it,
				$task_state,
				getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ? text(1013) : '',
				$parms
			);
 			
 			$task_it->moveNext();
 		}
 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(1242);
 	}
}