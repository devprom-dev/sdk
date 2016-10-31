<?php

include_once SERVER_ROOT_PATH."core/classes/sprites/UserPicSpritesGenerator.php";

class UpgradeUserPictures extends Installable 
{
    function check()
    {
        return true;
    }

    function install()
    {
		$generator = new UserPicSpritesGenerator();
		$generator->storeSprites();
        
        return true;
    }
}
