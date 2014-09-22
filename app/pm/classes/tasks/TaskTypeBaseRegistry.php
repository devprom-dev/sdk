<?php

class TaskTypeBaseRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    return " (SELECT t.* FROM pm_TaskType t WHERE t.VPD IS NULL) ";
 	}
}