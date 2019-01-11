<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessActionWorkflow.php";

class TaskBusinessActionResolveIssue extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '1327269011';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('ChangeRequest') == '' ) return true;
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;

        $request_it = $object_it->getRef('ChangeRequest');
        if ( $request_it->IsFinished() ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);
		getSession()->addBuilder( new RequestModelExtendedBuilder() );

		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();
        if ( $request_it->object->getAttributeType('OpenTasks') == '' ) return true;

		if ( !$request_it->getRef('OpenTasks')->end() ) return true; // if there are no open tasks then resolve an issue

		$service = new WorkflowService($request_it->object);
		$service->moveToState(
			$request_it,
			array_shift($request_it->object->getTerminalStates())
		);

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
