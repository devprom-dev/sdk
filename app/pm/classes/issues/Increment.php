<?php
include "IncrementRegistry.php";

class Increment extends Request
{
    function __construct() {
        parent::__construct( new IncrementRegistry($this) );
    }
}