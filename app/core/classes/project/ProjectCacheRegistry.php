<?php

class ProjectCacheRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		return " (SELECT t.pm_ProjectId, t.VPD, t.CodeName FROM pm_Project t) ";
	}
	
	function getSorts()
	{
		return array();
	}

	function getSortClause()
	{
		return "";
	}
}