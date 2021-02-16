<?php

class APIAccessPolicy extends AccessPolicy
{
 	function buildRoles() {
 		return array (0);
 	}

 	function getEntityAccess( $action_kind, &$object )
 	{
        if ( $object->getEntityRefName() == 'pm_Project' ) return true;

 		$value = getFactory()->getEntityOriginationService()->getSelfOrigin($object);
 		if ( $value == '' ) return true;

 		return $value != DUMMY_PROJECT_VPD;
 	}
}