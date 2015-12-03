<?php
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionSetPriorityHigh extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '644003759';
 	}
	
	function apply( $object_it )
 	{
 	    global $model_factory;
 	    
 	    if ( $object_it->get('Priority') != $object_it->object->getDefaultAttributeValue('Priority') ) return;
 	    
 	    $priority = $model_factory->getObject('Priority');
 	    
 	    $priority->setVpdContext($object_it);
 	    
 	    $priority_it = $priority->getAll();
 	    
 	    $priority_it->moveNext();
 	    
 	    $object_it->object->modify_parms($object_it->getId(), array(
 	            'Priority' => $priority_it->getId() 
 	    ));
 	    
 		return true;
 	}

 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1386);
 	}
}