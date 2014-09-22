<?php

include "WorkspaceRegistry.php";

class Workspace extends Metaobject
{
    function __construct()
    {
        parent::__construct('pm_Workspace', new WorkspaceRegistry());
    }
}