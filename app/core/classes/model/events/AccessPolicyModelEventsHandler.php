<?php

include_once "SystemTriggersBase.php";

class AccessPolicyModelEventsHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		$ref_name = $object_it->object->getEntityRefName();
		
		switch( $ref_name )
		{
		    case 'pm_Activity':
		    case 'pm_ProjectUse':
		    case 'Comment':
		    	return;
		    	
		    default:
				getFactory()->getAccessPolicy()->invalidateCache();		    	
		}
		
	}
}
 