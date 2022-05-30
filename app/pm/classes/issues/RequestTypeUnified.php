<?php
include "RequestTypeUnifiedRegistry.php";

class RequestTypeUnified extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('pm_IssueType', new RequestTypeUnifiedRegistry($this));
 	}

 	public function getIdAttribute() {
        return 'ReferenceName';
    }

    function IsPersistable() {
        return false;
    }
}
