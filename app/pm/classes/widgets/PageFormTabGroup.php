<?php
include "PageFormTabGroupRegistry.php";

class PageFormTabGroup extends CacheableSet
{
    function __construct() {
        parent::__construct(new PageFormTabGroupRegistry($this));
    }
}
