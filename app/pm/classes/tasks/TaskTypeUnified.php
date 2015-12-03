<?php
include "TaskTypeUnifiedRegistry.php";

class TaskTypeUnified extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('entity', new TaskTypeUnifiedRegistry($this));
 	}
}
