<?php

include "ObjectsListWidgetRegistry.php";

class ObjectsListWidget extends CacheableSet
{
    function __construct()
    {
        parent::__construct(new ObjectsListWidgetRegistry($this));
    }
}
