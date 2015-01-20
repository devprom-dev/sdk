<?php

include "AccountProductRegistry.php";

class AccountProduct extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new AccountProductRegistry($this));
    }
}