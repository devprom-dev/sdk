<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionReopenIssue extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '928368111';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('ChangeRequest') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

		$request_it = $object_it->getRef('ChangeRequest');
		if ( !in_array($request_it->get('State'), $request_it->object->getTerminalStates()) ) return true;
		
		$service = new WorkflowService($request_it->object);
		$service->moveToState(
				$request_it, 
				array_shift($request_it->object->getNonTerminalStates()), 
				str_replace('%2', $object_it->get('StateName'), 
						str_replace('%1', $object_it->object->getDisplayName(), text(1931))
				)
		);
 		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1932);
 	}
}
