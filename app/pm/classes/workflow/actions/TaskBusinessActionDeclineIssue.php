<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessAction.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";

class TaskBusinessActionDeclineIssue extends BusinessAction
{
 	function getId()
 	{
 		return '1362383505';
 	}
	
 	function apply( $object_it )
 	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;
		
 		if ( !$object_it->IsFailed() ) return true;

 		if ( $object_it->get('ChangeRequest') == '' ) return true;
 		
 		getSession()->addBuilder( new RequestModelExtendedBuilder() );
 		
 		$request_it = $object_it->getRef('ChangeRequest');

		$task_it = $request_it->getRef('OpenTasks');
		
		// if there is failed task then decline issue
		if ( $task_it->end() )
		{
			$resolution = text(906).': '.$object_it->get('Result');

			$service = new WorkflowService($request_it->object);
			
			$service->moveToState($request_it, array_shift($request_it->object->getNonTerminalStates()), $resolution);
		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1169);
 	}
} 
 