<?php
include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "AttributePermissionEntityRegistry.php";

class AttributePermissionEntity extends CacheableSet
{
    function __construct()
    {
        parent::__construct(new AttributePermissionEntityRegistry($this));
    }
}