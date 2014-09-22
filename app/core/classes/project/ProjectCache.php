<?php

include "ProjectCacheRegistry.php";

class ProjectCache extends MetaobjectCacheable
{
	public function __construct()
	{
		parent::__construct('pm_Project', new ProjectCacheRegistry());
	}

    function IsVPDEnabled()
	{
		return false;
	}
	
	function DeletesCascade( $object )
	{
		return false;
	}
	
	function IsDeletedCascade( $object )
	{
		return false;
	}
}
