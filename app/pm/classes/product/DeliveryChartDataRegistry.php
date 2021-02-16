<?php

class DeliveryChartDataRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
	 	$metastate = getFactory()->getObject('StateMeta');
		$metastate->setAggregatedStateObject(getFactory()->getObject('IssueState'));
 		$metastate->setStatesDelimiter("-");
 		
		$states = $metastate->getRegistry()->getAll()->fieldToArray('ReferenceName');
		$submitted = array_shift($states);

		$items = array(
			" SELECT DATE(IFNULL(t.StartDate, NOW())) StartDate, ".
			"		 CONCAT(DATE(IFNULL(t.DeliveryDate,NOW())),' 23:59:59') FinishDate, ".
			"		 (SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD) Project, ".
			"		 t.Caption, t.pm_FunctionId entityId, 'Feature' ObjectClass, t.RecordCreated, t.RecordModified, t.VPD, ".
			"		 IFNULL(CONCAT('Feature',(SELECT ft.ReferenceName FROM pm_FeatureType ft WHERE ft.pm_FeatureTypeId = t.Type)),'Feature') ObjectType, ".
			"		 0 Priority, t.Importance, IF(t.DeliveryDate IS NULL, '".$submitted."', NULL) State, ".
			"		 t.SortIndex, '' Custom1 ".
			"   FROM pm_Function t ".
 			"  WHERE 1 = 1 ".getFactory()->getObject('Feature')->getVpdPredicate('t'),
				
			" SELECT DATE(IFNULL(t.StartDate, NOW())), ".
			"		 CONCAT(DATE(IFNULL(t.DeliveryDate, NOW())),' 23:59:59'), ".
			"		 t.Project, t.Caption, t.pm_ChangeRequestId, 'Request', t.RecordCreated, t.RecordModified, t.VPD, ".
			"		 IFNULL(CONCAT('Request',(SELECT ft.ReferenceName FROM pm_IssueType ft WHERE ft.pm_IssueTypeId = t.Type)),'Request') ObjectType, t.Priority, 0, t.State, ".
			"		 (SELECT p.OrderNum FROM Priority p WHERE p.PriorityId = t.Priority), ".
			"		 (SELECT LCASE(p.ReferenceName) FROM pm_IssueType p WHERE p.pm_IssueTypeId = t.Type) ".
			"   FROM pm_ChangeRequest t, pm_Project pr ".
 			"  WHERE pr.pm_ProjectId = t.Project ".getFactory()->getObject('Request')->getVpdPredicate('t'),

			" SELECT IFNULL(t.MilestoneDate, NOW()), t.MilestoneDate, ".
			"		 (SELECT p.pm_ProjectId FROM pm_Project p WHERE p.VPD = t.VPD), ".
			"		 t.Caption, t.pm_MilestoneId, 'Milestone', t.RecordCreated, t.RecordModified, t.VPD, ".
			"		 'Milestone' ObjectType, 0, 0, NULL, 0 SortIndex, '' ".
			"   FROM pm_Milestone t ".
 			"  WHERE 1 = 1 ".getFactory()->getObject('Request')->getVpdPredicate('t'),
		);
 		
 	    return "(".join(" UNION ", $items).")";
 	}
}