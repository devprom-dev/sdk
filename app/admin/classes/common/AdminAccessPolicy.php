<?php

include_once SERVER_ROOT_PATH."co/classes/COAccessPolicy.php";

class AdminAccessPolicy extends COAccessPolicy
{
	function getEntityAccess( $action_kind, &$object )
	{
		if( !is_object($object) ) return true;

		$plugins = getFactory()->getPluginsManager();
		
		$array = is_object($plugins) ? $plugins->getPluginsForSection('admin') : array();
		foreach ( $array as $plugin ) {
			$result = $plugin->getEntityAccess( $action_kind, getSession()->getUserIt(), $object);
			if ( is_bool($result) ) return $result;
		}

		return parent::getEntityAccess( $action_kind, $object );
	}

	function getObjectAccess( $action_kind, &$object_it )
	{
		$class_name = $object_it->object->getClassName();
		
		switch ( $class_name )
		{
			case 'pm_ProjectRole':
				
				if ( $object_it->get('ReferenceName') == 'lead' )
				{
					return $action_kind != ACCESS_DELETE;
				}
				
			case 'pm_TaskType':
			case 'Priority':
            case 'pm_Severity':
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
			
		$plugins = getFactory()->getPluginsManager();
		
		$array = is_object($plugins) ? $plugins->getPluginsForSection('admin') : array();
		
		foreach ( $array as $plugin )
		{
			$result = $plugin->getObjectAccess( $action_kind, getSession()->getUserIt(), $object_it);
			
			if ( is_bool($result) ) return $result; 
		}

		return parent::getObjectAccess( $action_kind, $object_it );
	}
}
