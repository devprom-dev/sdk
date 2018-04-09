<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveIssueNextStateExt extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '2a200762-4d48-4131-9b58-b68620122321';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('IssueState') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);
		$request_it = $object_it->getRef('ChangeRequest');

		$types = getFactory()->getObject('IssueState')->getByRef('ReferenceName',$request_it->get('State'))->get('TaskTypes');
        if ( $types == '' ) return true;

        $taskStates =
            array_unique(
                getFactory()->getObject('Task')->getRegistry()->Query(
                    array(
                        new FilterAttributePredicate('TaskType', preg_split('/,/', $types)),
                        new FilterAttributePredicate('ChangeRequest', $request_it->getId()),
                    )
                )->fieldToArray('State')
            );
        if ( count($taskStates) > 1 ) return true;

        $transitionIt = $request_it->getStateIt()->getTransitionIt();
        while( !$transitionIt->end() )
        {
            if ( !$transitionIt->doable($request_it) ) {
                $transitionIt->moveNext();
                continue;
            }
            $service = new WorkflowService($request_it->object);
            $service->moveToState(
                $request_it,
                $transitionIt->getRef('TargetState')->get('ReferenceName')
            );
            return true;
        }

 		return false;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(2479);
 	}
}
