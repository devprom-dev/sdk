<?php

include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";
 
class COSystemTriggers extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		switch( $object_it->object->getEntityRefName() )
		{
			case 'cms_User':
				$generator = new UserPicSpritesGenerator();
				$generator->storeSprites();
                if ( array_key_exists('Language', $content) ) {
                    getFactory()->getCacheService()->invalidate();
                }
                getFactory()->getCacheService()->truncate('sessions');
                break;

			case 'co_ProjectGroup':
			case 'co_ProjectGroupLink':
                foreach( array('projects', 'sessions') as $path ) {
                    getFactory()->getCacheService()->truncate($path);
                }
				break;
		}
	}
}
