<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class SetWorkItemDatesTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( !in_array($object_it->object->getEntityRefName(), array('pm_Task', 'pm_ChangeRequest')) ) return;
	    if ( !in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) return;
	    
	    $this->processStartDate( $object_it, $content );
	    
	    if ( array_key_exists('State', $content) ) {
	    		$this->processFinishDate($object_it);
	    }
	    
	    if ( $object_it->object instanceof Request )
	    {
		    $service = new StoreMetricsService();
		    $request = new Request();
		    
	    	$service->storeIssueMetrics($request->getRegistry()->Query(
	    			array (
	    					new FilterInPredicate(array($object_it->getId())),
	    					new RequestMetricsPersister()
	    			)
	    		));
	    }
	}
	
	function processFinishDate( $object_it )
	{
		$value = in_array($object_it->get('State'), $object_it->object->getTerminalStates()) ? 'NOW()' : "NULL"; 

	    $table_name = $object_it->object->getEntityRefName();
	    
	    $sql = " UPDATE ".$table_name." SET FinishDate = ".$value." WHERE ".$table_name."Id = ".$object_it->getId();
	    
	    DAL::Instance()->Query($sql);
	}

	function processStartDate( $object_it, $content )
	{
	    $table_name = $object_it->object->getEntityRefName();

		switch( $table_name )
		{
		    case 'pm_Task':
		    	
		    	if ( $content['Release'] > 0 )
		    	{
		    		$value = " (SELECT GREATEST(r.StartDate, '".$object_it->get('RecordCreated')."') ".
		    				 "	  FROM pm_Release r WHERE r.pm_ReleaseId = pm_Task.Release) ";
		    	}
		    	
		    	break;
		    	
		    case 'pm_ChangeRequest':

				if ( $content['PlannedRelease'] > 0 )
		    	{
		    		$value = " (SELECT GREATEST(r.StartDate, '".$object_it->get('RecordCreated')."') ".
		    				 "	  FROM pm_Version r WHERE r.pm_VersionId = pm_ChangeRequest.PlannedRelease) ";
		    	}
		    	
		    	break;
		}
		
		// when the state is changed
		if ( $value == '' && array_key_exists('State', $content) )
		{
			$states = $object_it->object->getNonTerminalStates();
			
			// submitted
			if ( $object_it->get('State') == array_shift($states) ) $value = "NULL";
			
			// in queue
			if ( $value == '' && $object_it->get('State') == array_shift($states) ) $value = "NOW()";
		}
		
		if ( $value != '' )
		{
		    DAL::Instance()->Query("UPDATE ".$table_name." SET StartDate = ".$value." WHERE ".$table_name."Id = ".$object_it->getId());
		}
	}
}