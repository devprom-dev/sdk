<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "ChangeNotificationTypeRegistry.php";

class ChangeNotificationType extends CacheableSet
{
 	function __construct() 
 	{
 		parent::__construct(new ChangeNotificationTypeRegistry($this));
 	}
}
