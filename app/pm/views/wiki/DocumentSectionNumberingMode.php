<?php

include_once SERVER_ROOT_PATH."pm/classes/common/PMObjectCacheable.php";
include "DocumentSectionNumberingModeRegistry.php";

class DocumentSectionNumberingMode extends PMObjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new DocumentSectionNumberingModeRegistry($this));
    }
    
    function getDisplayName()
    {
        return translate('Нумерация');
    }
}