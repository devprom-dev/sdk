<?php
include "ObjectsListWidgetIterator.php";
include "ObjectsListWidgetRegistry.php";

class ObjectsListWidget extends CacheableSet
{
    function __construct() {
        parent::__construct(new ObjectsListWidgetRegistry($this));
    }

    function createIterator() {
        return new ObjectsListWidgetIterator($this);
    }
}
