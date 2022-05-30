<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once SERVER_ROOT_PATH . "pm/classes/workflow/actions/BusinessActionWorkflow.php";

class WikiPageBusinessActionChildrenSyncState extends BusinessActionWorkflow
{
 	function getId() {
 		return '57a8ce52-b930-11e4-a71e-12e3f512a338';
 	}

    function getDisplayName() {
        return text(2750);
    }
	
	function apply( $object_it )
 	{
 	    $childObject = getFactory()->getObject(get_class($object_it->object));
        $targetStateIt = $object_it->getRef('LastTransition')->getRef('TargetState');

		$page_it = $childObject->getRegistry()->Query(
            array (
                new FilterAttributePredicate('ParentPage', $object_it->getId())
            )
		);
		
		$service = new WorkflowService($childObject);
		while( !$page_it->end() )
		{
			try {
                $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
                    array (
                        new \TransitionSourceStatePredicate($page_it->get('State')),
                        new \FilterAttributePredicate('TargetState', $targetStateIt->getId())
                    )
                );
                if ( $transition_it->getId() != '' ) {
                    $service->moveByTransition(
                        $page_it, $transition_it, '', array(), true
                    );
                }
			}
			catch( Exception $e ) {
				Logger::getLogger('System')->error($e->getMessage());
                throw $e;
			}
			$page_it->moveNext();
		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		return null;
 	}
}
