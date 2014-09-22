<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class SetPlanItemDatesTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !in_array($object_it->object->getEntityRefName(), array('pm_Release', 'pm_Version')) ) return;

	    if ( !in_array($kind, array(TRIGGER_ACTION_MODIFY)) ) return;
	    
	    if ( !array_key_exists('StartDate', $content) ) return;
	    
	    $this->processWorkItemsStartDate($object_it);
	}
	
	function processWorkItemsStartDate( $object_it )
	{
		switch( $object_it->object->getEntityRefName() )
		{
		    case 'pm_Release':
		    	
		    	DAL::Instance()->Query(" UPDATE pm_Task t SET t.StartDate = (SELECT r.StartDate FROM pm_Release r WHERE r.pm_ReleaseId = t.Release) WHERE t.Release = ".$object_it->getId());
		    	
		    	break;
		    	
		    case 'pm_Version':
		    	
		    	DAL::Instance()->Query(" UPDATE pm_ChangeRequest t SET t.StartDate = (SELECT r.StartDate FROM pm_Version r WHERE r.pm_VersionId = t.PlannedRelease) WHERE t.PlannedRelease = ".$object_it->getId());
		    	
		    	break;
		}
	}
}