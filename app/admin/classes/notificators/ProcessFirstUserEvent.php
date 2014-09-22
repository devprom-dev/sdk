<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
 
class ProcessFirstUserEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $kind != TRIGGER_ACTION_ADD ) return;
		
		if ( $object_it->object->getEntityRefName() != 'cms_User' ) return;

		if ( $object_it->object->getRecordCount() > 1 ) return;

		getSession()->open( $object_it );
	}
}
