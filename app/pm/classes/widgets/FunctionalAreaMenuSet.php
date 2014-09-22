<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CacheableSet.php";
include "FunctionalAreaMenuRegistry.php";

class FunctionalAreaMenuSet extends CacheableSet
{
    function __construct()
    {
        parent::__construct(new FunctionalAreaMenuRegistry($this));
    }
}
