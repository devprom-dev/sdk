<?php

include_once "BusinessActionWorkflow.php";

class RequestBusinessActionAssignParticipant extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '555606649';
 	}
	
	function apply( $object_it )
 	{
        if ( $object_it->object->getAttributeType('Owner') == '' ) return;

        $data = $this->getData();
        $userId = getSession()->getUserIt()->getId();

        if ( $data['Owner'] > 0 || $data['Owner'] == $userId ) return true;
        if ( $object_it->get('Owner') == $userId ) return true;
 	    
 	    $object_it->object->modify_parms($object_it->getId(), 
 	    		array(
 	            	'Owner' => $userId
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
 		return text(1377);
 	}
}