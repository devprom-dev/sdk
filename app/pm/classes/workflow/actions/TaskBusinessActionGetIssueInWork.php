<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessActionWorkflow.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";

class TaskBusinessActionGetIssueInWork extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return 'cb453100-b374-47f8-8cd1-6096b98a3e99';
 	}
	
 	function apply( $object_it )
 	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;
 		if ( $object_it->get('ChangeRequest') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

 		getSession()->addBuilder( new RequestModelExtendedBuilder() );
 		$request_it = $object_it->getRef('ChangeRequest');

		$state_it = getFactory()->getObject('IssueState')->getRegistry()->Query(
			array(
				new FilterHasNoAttributePredicate('IsTerminal', 'Y'),
				new FilterVpdPredicate($request_it->get('VPD'))
			)
		);
		$state_it->moveNext(); // move to the second state (In Work)
		if ( $state_it->getId() > 0 ) {
			$service = new WorkflowService($request_it->object);
			$service->moveToState($request_it, $state_it->get('ReferenceName'));
		}
		else {
			throw new Exception('State corresponding to In Work was not found "'.$request_it->getId().'"');
		}
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(2064);
 	}
} 
 