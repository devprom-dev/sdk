<?php

include_once "BusinessAction.php";

class TaskBusinessActionAssignParticipant extends BusinessAction
{
 	function getId()
 	{
 		return '223125138';
 	}
	
	function apply( $object_it )
 	{
 	    if ( $object_it->get('Assignee') != '' ) return true;
 	    
 	    $object_it->object->modify_parms($object_it->getId(), 
 	    		array(
 	            	'Assignee' => getSession()->getUserIt()->getId() 
 	    		)
 	    	);
 	     		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1376);
 	}
}
