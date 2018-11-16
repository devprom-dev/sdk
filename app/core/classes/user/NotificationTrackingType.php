<?php
include "NotificationTrackingTypeRegistry.php";

class NotificationTrackingType extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new NotificationTrackingTypeRegistry($this));
 	}

    function getVpds() {
        return array();
    }

    function getCacheCategory() {
        return getSession()->getCacheKey($this->getVpdValue());         // participant-wide cache
    }
}
