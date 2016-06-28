<?php
include "WorkItemStateRegistry.php";

class WorkItemState extends Metaobject
{
 	function __construct() {
 		parent::__construct('pm_State', new WorkItemStateRegistry());
 	}
}