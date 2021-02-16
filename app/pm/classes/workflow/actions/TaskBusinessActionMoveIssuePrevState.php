<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveIssuePrevState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return 'c295bf41-09b7-409a-be08-2dd00653a83c';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('IssueState') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();

		if ( $request_it->get('StateObject') == '' ) return true;
        if ( getSession()->IsRDD() && $request_it->object instanceof Issue ) return true;


		$stateObjectIt = getFactory()->getObject('pm_StateObject')->getExact($request_it->get('StateObject'));
        $stateIt = $stateObjectIt->getRef('Transition')->getRef('SourceState');
		if ( $stateIt->get('ReferenceName') == '' ) return true;

		$service = new WorkflowService($request_it->object);
		$service->moveToState(
			$request_it,
            $stateIt->get('ReferenceName')
		);
 		
 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(2506);
 	}
}
