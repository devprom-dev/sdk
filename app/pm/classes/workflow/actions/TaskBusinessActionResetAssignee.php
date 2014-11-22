<?php

include_once "BusinessAction.php";

class TaskBusinessActionResetAssignee extends BusinessAction
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
 		global $model_factory;
 		return $model_factory->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1379);
 	}
}
