<?php

class ProjectRoleBaseRegistry extends ObjectRegistrySQL
{
  	function getQueryClause(array $parms)
 	{
 	    return " (SELECT t.* FROM pm_ProjectRole t WHERE t.VPD IS NULL) ";
 	}
}