<?php
include "AccountProductScrumBoardRegistry.php";

class AccountProductScrumBoard extends MetaobjectCacheable
{
    function __construct() {
        parent::__construct('entity', new AccountProductScrumBoardRegistry($this));
    }
}