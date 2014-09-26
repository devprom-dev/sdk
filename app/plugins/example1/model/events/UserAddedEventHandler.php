<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class UserAddedEventHandler extends ObjectFactoryNotificator
{
	function add( $object_it ) 
	{
		// handler is used only for users
	    if ( !$object_it instanceof UserIterator ) return;

	    Logger::getLogger('System')->error("Event handler is not implemented: ".get_class($this));
	    Logger::getLogger('System')->error("User data: ".var_export($object_it->getData(), true));
	}
	
	function modify( $prev_object_it, $object_it ) {} 

	function delete( $object_it ) {} 
}