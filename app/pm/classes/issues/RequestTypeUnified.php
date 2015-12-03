<?php
include "RequestTypeUnifiedRegistry.php";

class RequestTypeUnified extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new RequestTypeUnifiedRegistry($this));
 	}
}
