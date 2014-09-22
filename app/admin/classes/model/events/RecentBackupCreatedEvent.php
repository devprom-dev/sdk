<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class RecentBackupCreatedEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'cms_Backup' ) return;

	    getCheckpointFactory()->getCheckpoint('CheckpointSystem')->checkOnly( 
	    		array (
				    'CheckpointBackups'
	    		)
	    );
	}
}