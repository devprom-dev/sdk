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
			case 'pm_ChangeRequest':
			case 'pm_ChangeRequestTrace':
            case 'pm_Task':
			case 'pm_Question':
			case 'pm_Function':
			case 'pm_Attachment':
            case 'WikiPageFile':
			case 'Tag':
			case 'pm_RequestTag':
			case 'WikiTag':
			case 'pm_Watcher':
			case 'pm_Release':
			case 'pm_Milestone':
			case 'pm_Version':
            case 'pm_Build':
            case 'pm_Environment':
			case 'pm_Invitation':
			case 'BlogPost':
            case 'ObjectChangeLog':
            case 'sm_Person':
            case 'sm_Aim':
            case 'sm_Activity':
            case 'sm_Action':
            case 'pm_Workspace':
            case 'pm_WorkspaceMenu':
            case 'pm_WorkspaceMenuItem':
 	            return true;

 	        case 'pm_Methodology':
 	        case 'pm_AccessRight':
 	        case 'pm_CustomAttribute':
 	            return false;

			case 'pm_Task':
			case 'pm_TaskTrace':
				return $action_kind != ACCESS_CREATE;

            case 'co_ProjectGroup':
                return parent::getEntityAccess( $action_kind, $object );

 	        case 'pm_Project':
 	        	$access = parent::getEntityAccess( $action_kind, $object );
 	        	
 	        	if ( $access == false ) return $access;
 	        	
 	            return $action_kind == ACCESS_READ || $action_kind == ACCESS_CREATE;
 	            
 	        default:
				if ( $object instanceof ProjectPage ) {
					return true;
				}
 	            return $action_kind == ACCESS_READ;
 	    }
 	    
 	    return parent::getEntityAccess( $action_kind, $object );
 	}

 	function getRoleReferenceName( $role_id )
	{
	    return 'linkedguest';
	}
}
