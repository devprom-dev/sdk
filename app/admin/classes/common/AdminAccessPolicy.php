<?php

include_once SERVER_ROOT_PATH."co/classes/COAccessPolicy.php";

class AdminAccessPolicy extends COAccessPolicy
{
    var $license_it;
    
	function getEntityAccess( $action_kind, &$object )
	{
		if( is_object($object) )
		{
			$ref_name = $object->getClassName();
		}
		else
		{
			return true;
		}

		$plugins = getSession()->getPluginsManager();
		
		$array = is_object($plugins) ? $plugins->getPluginsForSection('admin') : array();
		
		foreach ( $array as $plugin )
		{
			$result = $plugin->getEntityAccess( $action_kind, getSession()->getUserIt(), $object);
			
			if ( is_bool($result) ) return $result;
		}

		if ( $action_kind == ACCESS_CREATE  )
		{
			if ( !is_object($this->license_it) )
			{
			    $this->license_it = getFactory()->getObject('LicenseInstalled')->getAll();
			}
			
		    if ( !$this->license_it->allowCreate( $object ) ) return false;
		}
		
		return parent::getEntityAccess( $action_kind, $object );
	}

	function getObjectAccess( $action_kind, &$object_it )
	{
		$class_name = $object_it->object->getClassName();
		
		switch ( $class_name )
		{
			case 'cms_User':
				
				if ( $action_kind == ACCESS_DELETE )
				{
					$this->setReason( text(917) );

					$it = $object_it->getParticipantIt();
					
					return $it->count() < 1; 
				}
				
				break;

			case 'pm_ProjectRole':
				
				if ( $object_it->get('ReferenceName') == 'lead' )
				{
					return $action_kind != ACCESS_DELETE;
				}
				
			case 'pm_TaskType':
			case 'Priority':
			case 'pm_Importance':
			case 'pm_IssueType':
			case 'pm_ChangeRequestLinkType':
			case 'pm_TestExecutionResult':
				if ( $action_kind == ACCESS_DELETE )
				{
					$this->setReason( text(918) );

					return !$object_it->hasReferencedRecords();
				}
				break;

			case 'cms_Language':
			case 'cms_SystemSettings':
			    return $action_kind != ACCESS_DELETE;
		}
			
		$plugins = getSession()->getPluginsManager();
		
		$array = is_object($plugins) ? $plugins->getPluginsForSection('admin') : array();
		
		foreach ( $array as $plugin )
		{
			$result = $plugin->getObjectAccess( $action_kind, getSession()->getUserIt(), $object_it);
			
			if ( is_bool($result) ) return $result; 
		}

		return parent::getObjectAccess( $action_kind, $object_it );
	}
}
