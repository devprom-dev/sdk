<?php


include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';
 
class AdminSystemTriggers extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		$session = getSession();

		switch( $object_it->object->getEntityRefName() )
		{
			case 'cms_User':
                $generator = new UserPicSpritesGenerator();
                $generator->storeSprites();

				if ( $kind == 'modify' ) {
					$session->drop();
				}
				else {
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
            case 'pm_Project':
                $this->invalidateCache();
				break;
			    
			case 'cms_SystemSettings':
				$this->invalidateCache();
				$this->executeCheckpoints();
				break;
		}
	}
	
	function executeCheckpoints()
	{
		$checkpoint_factory = getCheckpointFactory();
		$checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );
	    $checkpoint->checkOnly( array('CheckpointHasAdmininstrator', 'CheckpointSystemAdminEmail') );
	}

	function invalidateCache()
    {
        SessionBuilder::Instance()->invalidate();
        $session = getSession();
        $session->drop();
    }
}
 