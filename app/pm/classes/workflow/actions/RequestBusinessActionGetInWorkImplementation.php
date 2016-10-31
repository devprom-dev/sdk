<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionGetInWorkImplementation extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '1392172416';
 	}
	
	function apply( $object_it )
 	{
 	    $request_it = $object_it->object->getRegistry()->Query(
				array (
 	    				new RequestImplementationFilter($object_it->getId())
				)
		);

 	    while( !$request_it->end() )
 	    {
 	        $request = new Request();
 	        
 	        $duplicate_it = $request->getRegistry()->Query( 
 	        		array(new FilterInPredicate($request_it->getId())) 
 	        );
 	        
 	        $state_it = getFactory()->getObject('IssueState')->getRegistry()->Query(
 	        		array( 
 	        				new FilterHasNoAttributePredicate('IsTerminal', 'Y'),
 	        				new FilterVpdPredicate($duplicate_it->get('VPD'))
 	        		)
 	        );

 	        // move to the second state
 	        $state_it->moveNext();
 	        
 	        if ( $state_it->getId() > 0 )
 	        {
				$service = new WorkflowService($request);
				$service->moveToState($duplicate_it, $state_it->get('ReferenceName'));
 	        }
 	        else
 	        {
 	        	throw new Exception('There is no initial state for the issue "'.$duplicate_it->getId().'"');
 	        }
 	    
 	        $request_it->moveNext();
 	    }
 	    
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1388);
 	}
}