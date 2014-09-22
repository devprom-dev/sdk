<?php

include "WorkflowTimeScaleRegistry.php";

class WorkflowTimeScale extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new WorkflowTimeScaleRegistry($this));
    }
    
    function getDisplayName()
    {
        return translate('Масштаб');
    }
}