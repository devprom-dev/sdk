<?php
include "NotificationRegistry.php";

class Notification extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new NotificationRegistry($this));
 	}

    function getVpds()
    {
        return array();
    }
}
