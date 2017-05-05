<?php
include "IssueRegistry.php";

class Issue extends Request
{
    function __construct() {
        parent::__construct( new IssueRegistry($this) );
    }
}