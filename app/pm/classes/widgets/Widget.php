<?php

include "WidgetRegistry.php";

class Widget extends CacheableSet
{
    function __construct()
    {
        parent::__construct(new WidgetRegistry($this));
    }
}
