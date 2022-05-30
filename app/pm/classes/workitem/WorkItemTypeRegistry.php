<?php

class WorkItemTypeRegistry extends ObjectRegistrySQL
{
 	function getQueryClause(array $parms)
 	{
		$task = getFactory()->getObject('Task');
		$request = getSession()->IsRDD()
            ? getFactory()->getObject('Issue')
            : getFactory()->getObject('Request');

		$sql = "
			SELECT t.Caption pm_TaskTypeId,
				   t.Caption,
				   MIN(t.VPD) VPD,
				   MIN(t.OrderNum) OrderNum
			  FROM pm_TaskType t
			 WHERE 1 = 1 ".$task->getVpdPredicate('t')."
			 GROUP BY t.Caption
			 UNION
			SELECT t.Caption,
				   t.Caption,
				   MIN(t.VPD),
				   MIN(t.OrderNum)
			  FROM pm_IssueType t
			 WHERE 1 = 1 ".$request->getVpdPredicate('t')."
			 GROUP BY t.Caption 
			 UNION
			SELECT 'issue',
				   '".translate($request->getDisplayName())."',
				   '',
				   1
			 UNION
			SELECT 'reviewcenter',
				   '".translate('Согласование')."',
				   '',
				   1
		";

 	    return "(".$sql.")";
 	}
}