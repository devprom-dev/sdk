<?php

class ProjectRoleBaseRegistry extends ObjectRegistrySQL
{
  	function getQueryClause()
 	{
 	    return " (SELECT t.* FROM pm_ProjectRole t WHERE t.VPD IS NULL) ";
 	}
}