<?php
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
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
				// reset cached values
				getSession()->drop();
				break;

			case 'co_ProjectGroup':
			case 'co_ProjectGroupLink':
				getSession()->drop();
				break;

		}
	}
}
