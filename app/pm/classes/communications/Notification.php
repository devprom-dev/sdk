<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "NotificationRegistry.php";

class Notification extends PMObjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new NotificationRegistry($this));
 	}
}
