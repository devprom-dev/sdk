<?php
include_once "BusinessActionWorkflow.php";

class TaskBusinessActionResetAssignee extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '382318811';
 	}
	
	function apply( $object_it )
 	{
 	    if ( $object_it->get('Assignee') == '' ) return true;
 	    
 	    $object_it->object->modify_parms($object_it->getId(), array( 'Assignee' => '' ));
 	     		
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1379);
 	}
}
