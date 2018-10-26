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

            $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('SourceState', $state_it->getId()),
                    new SortOrderedClause()
                )
            );
            while( !$transition_it->end() ) {
                if ( !$transition_it->doable($request_it) ) {
                    $transition_it->moveNext();
                    continue;
                }
                $service = new WorkflowService($request_it->object);
                $service->moveByTransition( $request_it->copy(), $transition_it );
                break;
            }

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
