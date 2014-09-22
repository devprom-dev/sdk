<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "DocumentModeRegistry.php";

class DocumentMode extends PMObjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new DocumentModeRegistry($this));
    }
    
    function getDisplayName()
    {
        return translate('Режим');
    }
}