<?php
include "BugRegistry.php";

class Bug extends Metaobject
{
    function __construct()
    {
        parent::__construct('pm_ChangeRequest', new BugRegistry(), getSession()->getCacheKey());
    }
}