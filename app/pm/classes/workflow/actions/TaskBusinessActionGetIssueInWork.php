<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessActionWorkflow.php";

class TaskBusinessActionGetIssueInWork extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return 'cb453100-b374-47f8-8cd1-6096b98a3e99';
 	}
	
 	function apply( $object_it )
 	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;
 		if ( $object_it->get('ChangeRequest') == '' ) return true;

		$request = getFactory()->getObject('Request');
		getFactory()->resetCachedIterator($request);

 		getSession()->addBuilder( new RequestModelExtendedBuilder() );
 		$request_it = $object_it->getRef('ChangeRequest')->getSpecifiedIt();

        if ( getSession()->IsRDD() && $request_it->object instanceof Issue ) return true;

		$state_it = getFactory()->getObject($request_it->object->getStateClassName())->getRegistry()->Query(
			array(
				new FilterAttributePredicate('IsTerminal', 'I'),
				new FilterVpdPredicate($request_it->get('VPD')),
                new SortOrderedClause()
			)
		);
		if ( $state_it->getId() > 0 ) {
			$service = new WorkflowService($request_it->object);
			$service->moveToState($request_it, $state_it->get('ReferenceName'));
		}
		else {
			throw new Exception('State corresponding to In Work was not found "'.$request_it->getId().'"');
		}
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(2064);
 	}
} 
 