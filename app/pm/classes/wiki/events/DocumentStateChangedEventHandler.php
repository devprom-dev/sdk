<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/events/WorklfowMovementEventHandler.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/WikiPageBusinessActionChildrenSyncState.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/WikiPageBusinessActionDocumentSyncState.php";

class DocumentStateChangedEventHandler extends WorklfowMovementEventHandler
{
	function readyToHandle()
	{ 
		return $this->getObjectIt()->object instanceof WikiPage && $this->getObjectIt()->object->getStateClassName() != '';  
	}
	
	function handle( $object_it )
	{
		getFactory()->getEventsManager()->removeNotificator($this);
		
		$action = new WikiPageBusinessActionChildrenSyncState();
	    $action->apply( $object_it );
	    
	    $action = new WikiPageBusinessActionDocumentSyncState();
	    $action->apply( $object_it );
	}
}