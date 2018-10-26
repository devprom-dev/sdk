<?php

include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
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

                $this->invalidateCache(array('projects','apps','sessions'));
				$this->executeCheckpoints();
				break;
				
			case 'cms_BlackList':
            case 'co_AccessRight':
            case 'co_UserGroupLink':
            case 'Priority':
            case 'pm_Importance':
                $this->invalidateCache(array('projects','apps','sessions'));
                break;

			case 'co_ProjectGroupLink':
            case 'co_ProjectGroup':
			case 'cms_PluginModule':
			case 'cms_Update':
                $this->invalidateGlobalCache();
				break;

            case 'cms_License':
                $this->invalidateGlobalCache();
                InstallationFactory::getFactory();
                foreach( array(new CacheParameters()) as $command ) {
                    $command->install();
                }
                break;

            case 'pm_Project':
                $this->invalidateCache(array('projects','sessions'));
                break;

			case 'cms_SystemSettings':
				$this->invalidateGlobalCache();
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

	function invalidateCache( array $paths ) {
        foreach( $paths as $path ) {
            getFactory()->getCacheService()->invalidate($path);
        }
    }
	function invalidateGlobalCache() {
        getFactory()->getCacheService()->invalidate();
    }
}
 