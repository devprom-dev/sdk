<?php

include "AccountProductSupportRegistry.php";

class AccountProductSupport extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new AccountProductSupportRegistry($this));
    }
}