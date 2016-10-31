<?php

include_once "ProjectImportanceRegistry.php";

class ProjectImportance extends MetaobjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new ProjectImportanceRegistry($this));
	}

    function getVpds()
    {
        return array();
    }
}