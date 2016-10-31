<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/events/WorklfowMovementEventHandler.php";

class ResetTasksEventHandler extends WorklfowMovementEventHandler
{
	function readyToHandle() {
		return $this->getObjectIt()->object instanceof Request;  
	}
	
	function handle( $object_it )
	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) {
			if ( in_array($object_it->get('State'), $object_it->object->getTerminalStates()) ) {
				$actions_it = getFactory()->getObject('StateBusinessAction')->getAll();
				$action = new RequestBusinessActionResolveTasks();
				$action->apply( $object_it );
			}
		}
	}
}