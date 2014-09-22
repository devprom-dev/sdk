<?php

 class APIAccessPolicy extends AccessPolicy
 {
 	function buildRoles()
 	{
 		return array (0);
 	}
 	
 	function getEntityAccess( $action_kind, &$object ) 
 	{
 		$value = getFactory()->getEntityOriginationService()->getSelfOrigin($object);
 		
 		if ( $value == '' ) return true; 

 		return $value != DUMMY_PROJECT_VPD;
 	}
	
 	function getObjectAccess( $action_kind, &$object_it )
 	{ 
		return true;
 	}
 }