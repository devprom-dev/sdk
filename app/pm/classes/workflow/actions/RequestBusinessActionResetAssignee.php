<?php

include_once "BusinessAction.php";

class RequestBusinessActionResetAssignee extends BusinessAction
{
 	function getId()
 	{
 		return '1787828805';
 	}
	
	function apply( $object_it )
 	{
 	    if ( $object_it->get('Owner') == '' ) return true;
 	    
 	    $object_it->modify( array( 'Owner' => '' ) );
 	    
 		return true;
 	}

 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1379);
 	}
}