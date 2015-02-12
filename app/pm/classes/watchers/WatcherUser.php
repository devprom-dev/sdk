<?php

include "WatcherUserRegistry.php";

class WatcherUser extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('cms_User', new WatcherUserRegistry($this));
 	}
}