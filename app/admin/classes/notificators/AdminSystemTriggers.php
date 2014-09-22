<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
 
class AdminSystemTriggers extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		global $model_factory, $session;
		
		$entity_ref_name = $object_it->object->getEntityRefName();

		switch( $entity_ref_name )
		{
			case 'cms_User':
				
				if ( $kind == 'modify' )
				{
					$session->drop();
				}
				else
				{
					$session->truncate('usr');
				}
				
				$this->executeCheckpoints();
				
				break;
				
			case 'cms_BlackList':
				
				$session->truncate('usr');

				break;
				
			case 'co_AccessRight':
			case 'co_UserGroupLink':
			case 'co_ProjectGroupLink':
			case 'cms_PluginModule':
			case 'cms_License':
			case 'cms_Update':
			    
				$session->drop();
				
				break;
			    
			case 'cms_SystemSettings':
				
				$session->drop();

				$this->executeCheckpoints();

				break;
		}
	}
	
	function executeCheckpoints()
	{
		$checkpoint_factory = getCheckpointFactory();
		
		$checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );

	    $checkpoint->checkOnly( array('CheckpointHasAdmininstrator', 'CheckpointSystemAdminEmail', 'CheckpointWindowsSMTP') );
	}
}
 