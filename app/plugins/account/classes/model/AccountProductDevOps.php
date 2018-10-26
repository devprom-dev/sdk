<?php
include "AccountProductDevOpsRegistry.php";

class AccountProductDevOps extends MetaobjectCacheable
{
    function __construct()
    {
        parent::__construct('entity', new AccountProductDevOpsRegistry($this));
    }
}