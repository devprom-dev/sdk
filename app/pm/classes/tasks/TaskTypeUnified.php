<?php
include "TaskTypeUnifiedRegistry.php";

class TaskTypeUnified extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('pm_TaskType', new TaskTypeUnifiedRegistry($this));
 	}

 	function IsPersistable() {
        return false;
    }
}
