<?php
include "ProjectLinkedRegistry.php";

class ProjectLinked extends Metaobject
{
	function __construct() {
		parent::__construct('pm_Project', new ProjectLinkedRegistry());
	}

    function getVpdValue() {
        return '';
    }
}