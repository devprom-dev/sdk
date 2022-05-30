<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveTracesNextState extends BusinessActionWorkflow
{
 	function getId() {
 		return 'a470d9c7-028a-4a57-b93b-d8c7028ce7cc';
 	}
	
	function apply( $object_it )
 	{
 	    $traceIt = getFactory()->getObject('TaskTraceWikiPage')->getRegistry()->Query(
 	        array(
 	            new FilterAttributePredicate('Task', $object_it->getId()),
                new FilterAttributePredicate('ObjectClass', array('Requirement', 'TestScenario', 'HelpPage'))
            )
        );

 	    while( !$traceIt->end() ) {
 	        $pageIt = $traceIt->getObjectIt();
 	        if ( $pageIt->object instanceof \MetaobjectStatable && $pageIt->object->getStateClassName() != '' ) {
                $stateIt = workflowScheme::Instance()->getStateIt($pageIt);
                $stateIt->moveTo('ReferenceName', $pageIt->get('State'));
                $stateIt->moveNext();
                if ( $stateIt->get('ReferenceName') != '' ) {
                    $service = new WorkflowService($pageIt->object);
                    $service->moveToState(
                        $pageIt,
                        $stateIt->get('ReferenceName')
                    );
                }
            }
            $traceIt->moveNext();
        }

 		return true;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(3013);
 	}
}
