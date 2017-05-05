<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionMoveImplementedNextState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '6c22bd92-8af5-4644-91b6-84f2219b57e7';
 	}
	
	function apply( $object_it )
 	{
		$request_it = $object_it->object->getRegistry()->Query(
			array (
				new RequestImplementationFilter($object_it->getId()),
                new FilterNotInPredicate($object_it->getId())
			)
		);
		while( !$request_it->end() )
		{
            $state_it = $request_it->getStateIt();
            if ( $state_it->get('IsTerminal') == 'Y' ) {
                $request_it->moveNext();
                continue;
            }

            $transition_it = $state_it->getTransitionIt();
            if ( !$transition_it->appliable() || !$transition_it->doable($request_it) ) {
                $request_it->moveNext();
                continue;
            }

			$service = new WorkflowService($request_it->object);
			$service->moveToState(
                $request_it->copy(),
                $transition_it->getRef('TargetState')->get('ReferenceName')
			);

			$request_it->moveNext();
		}
 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
 	function getDisplayName() {
 		return text(2102);
 	}
}
