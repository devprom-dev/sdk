<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class TaskAddedEventHandler extends ObjectFactoryNotificator
{
	function add( $object_it ) 
	{
		// handler is used only for users
	    if ( !$object_it instanceof TaskIterator ) return;

	    // skip handler if there is no related issue defined
	    if ( $object_it->get('ChangeRequest') == '' ) return;
	    
	    $request_it = $object_it->getRef('ChangeRequest');
	    
	    $service = new WorkflowService($request_it->object);
	    
	    $service->moveToState( $request_it, 'planned', "Moved after task was created" );
	}
	
	function modify( $prev_object_it, $object_it ) {} 

	function delete( $object_it ) {} 
}