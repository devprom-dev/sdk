<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveIssue extends BusinessActionWorkflow
{
 	function getId() {
 		return '067c2a46-e65d-481d-b2c0-e32bcdd8f4db';
 	}
	
	function apply( $object_it )
 	{
		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();
		$state_it = workflowScheme::Instance()->getStateIt($request_it);

		$state_it->moveTo('Caption', $this->getParameters());
        if ( $state_it->getId() == '' ) {
            $state_it->moveTo('ReferenceName', $this->getParameters());
            if ( $state_it->getId() == '' ) return false;
        }

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
 		return text(3211);
 	}
}
