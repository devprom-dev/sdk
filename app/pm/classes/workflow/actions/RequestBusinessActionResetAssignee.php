<?php
include_once "BusinessActionWorkflow.php";

class RequestBusinessActionResetAssignee extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '1787828805';
 	}
	
	function apply( $object_it )
 	{
 		if ( $object_it->object->getAttributeType('Owner') == '' ) return;
 		
 	    if ( $object_it->get('Owner') == '' ) return true;
 	    
 	    $object_it->object->modify_parms($object_it->getId(), array( 'Owner' => '' ));
 	    
 		return true;
 	}

 	function getObject()
 	{
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1379);
 	}
}