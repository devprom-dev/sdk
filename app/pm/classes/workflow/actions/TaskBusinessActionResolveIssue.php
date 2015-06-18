<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessAction.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";

class TaskBusinessActionResolveIssue extends BusinessAction
{
 	function getId()
 	{
 		return '1327269011';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('ChangeRequest') == '' ) return true;
 		
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;
		
		getSession()->addBuilder( new RequestModelExtendedBuilder() );
		
		$request_it = $object_it->getRef('ChangeRequest');
 		
		$task_it = $request_it->getRef('OpenTasks');

		// if there are no open tasks then resolve an issue
		if ( $task_it->end() )
		{
			$resolution = translate('Результат').': '.$object_it->get('Result');

			$terminals = $request_it->object->getTerminalStates();
			
			$service = new WorkflowService($request_it->object);
			
			$service->moveToState($request_it, $terminals[0], $resolution);
		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1168);
 	}
}
