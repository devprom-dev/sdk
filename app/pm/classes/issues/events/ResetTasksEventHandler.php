<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/events/WorklfowMovementEventHandler.php";

class ResetTasksEventHandler extends WorklfowMovementEventHandler
{
	function readyToHandle()
	{ 
		return $this->getObjectIt()->object instanceof Request;  
	}
	
	function handle( $object_it )
	{
		if ( $object_it->get('State') != 'submitted' ) return;
		
	    $actions_it = getFactory()->getObject('StateBusinessAction')->getAll();
	    
	    $reset_action = new RequestBusinessActionResetTasks();
	    
	    $reset_action->apply( $object_it );
	}
}