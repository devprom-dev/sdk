<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "NotificationTrackingTypeRegistry.php";

class NotificationTrackingType extends CacheableSet
{
 	function __construct() {
 		parent::__construct(new NotificationTrackingTypeRegistry($this));
 	}
}
