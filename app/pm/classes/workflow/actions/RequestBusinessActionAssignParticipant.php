<?php

include_once "BusinessAction.php";

class RequestBusinessActionAssignParticipant extends BusinessAction
{
 	function getId()
 	{
 		return '555606649';
 	}
	
	function apply( $object_it )
 	{
 		if ( $object_it->object->getAttributeType('Owner') == '' ) return;
 		
 	    if ( $object_it->get('Owner') != '' ) return true;
 	    
 	    $participant = getFactory()->getObject('pm_Participant');
 	    
 	    $participant->setVpdContext($object_it);
 	    
 	    $participant_it = $participant->getByRef('SystemUser', getSession()->getUserIt()->getId());
 	    
 	    $object_it->modify( array(
 	            'Owner' => $participant_it->getId() 
 	    ));
 	    
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1376);
 	}
}