<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class TaskChangedEventHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		// handler is used only for tasks
	    if ( !$object_it instanceof TaskIterator ) return;

	    Logger::getLogger('System')->error("Event handler is not implemented: ".get_class($this));
	    Logger::getLogger('System')->error("Action type was: ".$kind);
	    Logger::getLogger('System')->error("Data: ".var_export($content, true));
	}
}