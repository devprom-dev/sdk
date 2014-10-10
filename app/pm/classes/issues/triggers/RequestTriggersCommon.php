<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RequestTriggersCommon extends SystemTriggersBase
{
    function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_MODIFY ) return;

	    if ( $object_it->object->getEntityRefName() != 'pm_ChangeRequest' ) return;
	    
		if ( !array_key_exists('State', $content) ) return;

		if ( $object_it->get('State') != 'submitted' ) return;
		
	    if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return;
		
	    $this->stateAttributeModified( $object_it );
	}
    
	function stateAttributeModified( $object_it )
	{
		$task = getFactory()->getObject('pm_Task');
		
 		$task->removeNotificator( 'EmailNotificator' );
 		
 		$task_it = $task->getByRefArray( array( 
 		        'Assignee' => getSession()->getParticipantIt()->getId(),
 				'ChangeRequest' => $object_it->getId() 
 		));

		$service = new WorkflowService($task);
 		
 		while ( !$task_it->end() )
 		{
			$service->moveToState($task_it, 'planned');
 			
 			$task_it->moveNext();
 		}
	}
}
 