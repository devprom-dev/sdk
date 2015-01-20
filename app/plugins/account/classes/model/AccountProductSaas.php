<?php

include "AccountProductSaasRegistry.php";

class AccountProductSaas extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new AccountProductSaasRegistry($this));
    }
}