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
 	    
 	    $object_it->object->modify_parms($object_it->getId(), 
 	    		array(
 	            	'Owner' => getSession()->getUserIt()->getId() 
 	    		)
 	    	);
 	    
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