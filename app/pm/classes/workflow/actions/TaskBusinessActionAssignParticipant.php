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
 	    global $model_factory;
 	    
 	    if ( $object_it->get('Assignee') != '' ) return true;
 	    
 	    $participant = $model_factory->getObject('pm_Participant');
 	    
 	    $participant->setVpdContext($object_it);
 	    
 	    $participant_it = $participant->getByRef('SystemUser', getSession()->getUserIt()->getId());
 	    
 	    $object_it->object->modify_parms($object_it->getId(), array(
 	            'Assignee' => $participant_it->getId() 
 	    ));
 	     		
 		return true;
 	}

 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1376);
 	}
}
