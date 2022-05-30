<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionMoveIssueNextStateByTaskTypes extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '4f7966aa-2d25-48cb-9b21-3985e6c61936';
 	}
	
	function apply( $object_it )
 	{
		if ( $object_it->get('IssueState') == '' ) return true;

		$nextTaskIt = getFactory()->getObject('Task')->getRegistry()->Query(
		    array(
		        new FilterNotInPredicate($object_it->getId()),
                new FilterAttributePredicate('ChangeRequest', $object_it->get('ChangeRequest')),
                new StatePredicate('notresolved'),
                new SortOrderedClause()
            )
        );

		$nextTypeId = $nextTaskIt->get('TaskType');
		if ( $nextTypeId == '' ) return false;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);
		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();

		$issueStateIt = getFactory()->getObject($request_it->object->getStateClassName())->getAll();
		while( !$issueStateIt->end() ) {
		    $types = \TextUtils::parseIds($issueStateIt->get('TaskTypes'));
		    if ( in_array($nextTypeId, $types) ) {
                $service = new WorkflowService($request_it->object);
                $service->moveToState( $request_it, $issueStateIt->get('ReferenceName') );
                return true;
            }
            $issueStateIt->moveNext();
        }

 		return false;
 	}

 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(3132);
 	}
}
