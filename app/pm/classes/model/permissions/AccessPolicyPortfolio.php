<?php

include_once "AccessPolicyBase.php";

class AccessPolicyPortfolio extends AccessPolicyBase
{
	protected function buildRoles()
	{
		return array( 1 => 0 );
	}
	
 	function getEntityAccess( $action_kind, & $object ) 
 	{
 	    switch ( $object->getClassName() )
 	    {
 	        case 'pm_UserSetting':
 	        case 'Comment':
 	        case 'pm_Activity':
 	        case 'pm_CustomReport':
 	        case 'cms_Report':
 	            return true;
 	            
 	        case 'pm_Methodology':
 	        case 'pm_AccessRight':
 	        case 'pm_CustomAttribute':
 	        case 'pm_VersionSettings':
 	            return false;

 	        case 'pm_ChangeRequest':
 	        case 'pm_Task':
 	            return $action_kind != ACCESS_CREATE;

 	        case 'pm_Project':
 	            return $action_kind == ACCESS_READ || $action_kind == ACCESS_CREATE;
 	            
 	        default:
 	            return $action_kind == ACCESS_READ;
 	    }
 	    
 	    return parent::getEntityAccess( $action_kind, $object );
 	}

 	function getRoleReferenceName( $role_id )
	{
	    return 'linkedguest';
	}
}
