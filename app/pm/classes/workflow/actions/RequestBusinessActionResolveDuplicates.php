<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once "BusinessAction.php";

class RequestBusinessActionResolveDuplicates extends BusinessAction
{
 	function getId()
 	{
 		return '706204028';
 	}
	
	function apply( $object_it )
 	{
 	    $request = $this->getObject();
 		
 		$duplicate_it = $request->getRegistry()->Query(
				array (
 	    				new RequestDuplicatesOfFilter($object_it->getId())
				)
		);
 		 	    
 	    while( !$duplicate_it->end() )
 	    {
 	        $state_it = getFactory()->getObject('IssueState')->getRegistry()->Query(
 	        		array( 
 	        				new FilterAttributePredicate('IsTerminal', 'Y'),
 	        				new FilterVpdPredicate($duplicate_it->get('VPD'))
 	        		)
 	        );
 	        
 	        if ( $state_it->getId() > 0 )
 	        {
				$service = new WorkflowService($request);
				
				$service->moveToState($duplicate_it, $state_it->get('ReferenceName'), $this->getDisplayName());
 	        }
 	        else
 	        {
 	        	throw new Exception('There is no terminal state for the issue "'.$duplicate_it->getId().'"');
 	        }
 	    
 	        $duplicate_it->moveNext();
 	    }
 	    
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1387);
 	}
}