<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RequestTriggersCommon extends SystemTriggersBase
{
    function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_MODIFY ) return;

	    if ( $object_it->object->getEntityRefName() != 'pm_ChangeRequest' ) return;
	    
		if ( !array_key_exists('State', $content) ) return;

		$this->stateAttributeModified( $object_it );
	}
    
	function stateAttributeModified( $object_it )
	{
	    global $model_factory;
	    
	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
	    
	    $part_it = getSession()->getParticipantIt();
	    
	    switch ( $object_it->get('State') )
	    {
	        case 'submitted':

	    		if ( is_object($methodology_it) && !$methodology_it->HasTasks() )
				{
					$task = $model_factory->getObject('pm_Task');
					
			 		$task->removeNotificator( 'EmailNotificator' );
			 		
			 		$task_it = $task->getByRefArray( array( 
			 		        'Assignee' => $part_it->getId(),
			 				'ChangeRequest' => $object_it->getId() 
			 		));
			 			
			 		while ( !$task_it->end() )
			 		{
			 			$task_it->modify( array('State' => 'planned') );
			 			
			 			$task_it->moveNext();
			 		}
				}
	            
	            break;
	    }
	}
}
 