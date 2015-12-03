<?php

class WorkItemTypeRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
		$task = getFactory()->getObject('Task');
		$request = getFactory()->getObject('Request');

		$sql = "
			SELECT t.pm_TaskTypeId pm_TaskTypeId,
				   t.Caption,
				   t.ReferenceName,
				   t.VPD,
				   t.OrderNum
			  FROM pm_TaskType t
			 WHERE 1 = 1 ".$task->getVpdPredicate('t')."
			 UNION
			SELECT 1000000 + t.pm_IssueTypeId,
				   t.Caption,
				   t.ReferenceName,
				   t.VPD,
				   t.OrderNum
			  FROM pm_IssueType t
			 WHERE 1 = 1 ".$request->getVpdPredicate('t')."
			 UNION
			SELECT 1000000,
				   '".translate($request->getDisplayName())."',
				   'issue',
				   '',
				   1
		";

 	    return "(".$sql.")";
 	}
}