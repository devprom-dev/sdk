<?php



class TaskOrderNumTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_Task' ) return;
	    
	    if ( !in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) return;

		$wasData = $this->getWasData();
	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$to_process_ordernum =  $methodology_it->get('IsRequestOrderUsed') == 'Y'
			&& $wasData['OrderNum'] != $object_it->get('OrderNum');
		if ( !$to_process_ordernum ) return;
		
		$this->processOrderNum($object_it);
	}
	
	function processOrderNum( $object_it )
	{
		$registry = $object_it->object->getRegistry();
		$registry->setPersisters(array());
		$seq_it = $registry->Query(
			array (
				new FilterNextSiblingsPredicate($object_it),
				new FilterBaseVpdPredicate(),
				new StatePredicate('notresolved'),
				new SortOrderedClause()
			)
		);
		if ( $seq_it->count() < 1 ) return;
		$ids = $seq_it->idsToArray();

		$sql = "SET @r=".($object_it->get('OrderNum') > 0 ? $object_it->get('OrderNum') : 0);
		DAL::Instance()->Query( $sql );
		
		$sql = "UPDATE pm_Task t SET t.OrderNum = @r:= (@r+1), t.RecordModified = NOW() WHERE t.pm_TaskId IN (".join(",", $ids).") ORDER BY t.OrderNum ASC";
		DAL::Instance()->Query( $sql );
		
		$sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
		       " SELECT NOW(), NOW(), t.VPD, t.pm_TaskId, 'Task' ".
		       "     FROM pm_Task t WHERE t.pm_TaskId IN (".join(",", array_slice($ids,0,20)).") ";
		DAL::Instance()->Query( $sql );
	}
}
 