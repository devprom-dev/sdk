<?php

class ProcessFirstUserEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $kind != TRIGGER_ACTION_ADD ) return;
		
		if ( $object_it->object->getEntityRefName() != 'cms_User' ) return;

		if ( $object_it->object->getRecordCount() > 1 ) return;

        getSession()->setAuthenticationFactory(null);
		getSession()->open( $object_it );
	}
}
