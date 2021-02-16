<?php
include "RequestWatcherRegistry.php";

class RequestWatcher extends Metaobject
{
    function __construct() {
        parent::__construct('pm_Watcher', new RequestWatcherRegistry($this));
        $this->setAttributeType('ObjectId', 'REF_RequestId');
    }

    function getDefaultAttributeValue($name) {
        return $name == 'ObjectClass' ? 'Request' : parent::getDefaultAttributeValue($name);
    }
}