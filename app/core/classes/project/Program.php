<?php

include_once SERVER_ROOT_PATH."core/classes/project/Project.php";
include "ProgramRegistry.php";

class Program extends Project
{
    function __construct()
    {
        parent::__construct( new ProgramRegistry($this) );
    }
}
