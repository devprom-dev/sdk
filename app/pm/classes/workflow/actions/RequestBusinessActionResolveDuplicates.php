<?php

include_once "BusinessAction.php";

class RequestBusinessActionResolveDuplicates extends BusinessAction
{
 	function getId()
 	{
 		return '706204028';
 	}
	
	function apply( $object_it )
 	{
 	    $request_it = $object_it->object->getRegistry()->Query(
				array (
 	    				new RequestDuplicatesOfFilter($object_it->getId())
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
 	        				new FilterAttributePredicate('IsTerminal', 'Y'),
 	        				new FilterVpdPredicate($duplicate_it->get('VPD'))
 	        		)
 	        );
 	        
 	        if ( $state_it->getId() > 0 )
 	        {
 	            $duplicate_it->modify( array ( 'State' => $state_it->get('ReferenceName') ) );
 	        }
 	        else
 	        {
 	        	throw new Exception('There is no terminal state for the issue "'.$duplicate_it->getId().'"');
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
 		return text(1387);
 	}
}