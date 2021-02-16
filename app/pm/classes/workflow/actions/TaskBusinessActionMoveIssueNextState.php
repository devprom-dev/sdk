<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveIssueNextState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return 'e2221ae5-2b36-4650-b842-16bb9ce98e1d';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('IssueState') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();

		if ( $request_it->get('OpenTasks') != '' ) return true;
        if ( getSession()->IsRDD() && $request_it->object instanceof Issue ) return true;

		$state_it = workflowScheme::Instance()->getStateIt($request_it);
		$state_it->moveTo('ReferenceName', $object_it->get('IssueState'));
		$state_it->moveNext();
		if ( $state_it->get('ReferenceName') == '' ) return true;

		$service = new WorkflowService($request_it->object);
		$service->moveToState(
			$request_it,
			$state_it->get('ReferenceName')
		);
 		
 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(2101);
 	}
}
