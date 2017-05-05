<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionIssueAutoActionShift.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionIssueAutoActionWorkflow.php";

class AutoActionEventHandler extends ObjectFactoryNotificator
{
    private $eventTypes = array(
        AutoActionEventRegistry::CreateAndModify,
        AutoActionEventRegistry::CreateOnly
    );

 	function add( $object_it ) 
	{
		if ( $object_it->object instanceof AutoAction ) {
            if ( !in_array($object_it->get('EventType'), $this->eventTypes) ) return;
            $this->applyAutoAction($object_it);
        }
        if ( $object_it->object instanceof Comment ) {
		    $anchorIt = $object_it->getAnchorIt();
		    if ( $anchorIt->object instanceof Request ) {
		        $this->applyAutoActionsOnComment($anchorIt);
            }
        }
    }

 	function modify( $prev_object_it, $object_it ) 
	{
		if ( $object_it->object instanceof AutoAction ) {
            if ( !in_array($object_it->get('EventType'), $this->eventTypes) ) return;
            $this->applyAutoAction($object_it);
        }
        if ( $object_it->object instanceof Request ) {
		    $this->applyAutoActionsOnModification($object_it);
        }
	}

 	function delete( $object_it )
 	{ 
 	}
 	
 	function applyAutoAction( $object_it )
 	{ 
 		$subject = getFactory()->getObject($object_it->object->getSubjectClassName());
 		$first_state = array_shift($subject->getNonTerminalStates());
 		
 		$subject_it = $subject->getRegistry()->Query(
            array (
                new StatePredicate($first_state),
                new FilterBaseVpdPredicate()
            )
 		);

 		$action = new BusinessActionIssueAutoActionShift($object_it);
 		while( !$subject_it->end() )
 		{
 			$action->applyContent($subject_it->copy(), array());
 			$subject_it->moveNext();
 		}
 		
        $lock = new LockFileSystem(get_class($subject));
        $lock->Release();
 	}

 	function applyAutoActionsOnComment( $object_it )
    {
        $actionIt = getFactory()->getObject('IssueAutoAction')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('EventType', AutoActionEventRegistry::NewComment),
                new FilterBaseVpdPredicate()
            )
        );
        while( !$actionIt->end() ) {
            $action = new BusinessActionIssueAutoActionWorkflow($actionIt);
            $action->apply($object_it);
            $actionIt->moveNext();
        }
    }

    function applyAutoActionsOnModification( $object_it )
    {
        $actionIt = getFactory()->getObject('IssueAutoAction')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('EventType', AutoActionEventRegistry::ModifyOnly),
                new FilterBaseVpdPredicate()
            )
        );
        while( !$actionIt->end() ) {
            $action = new BusinessActionIssueAutoActionWorkflow($actionIt);
            $action->apply($object_it);
            $actionIt->moveNext();
        }
    }
}