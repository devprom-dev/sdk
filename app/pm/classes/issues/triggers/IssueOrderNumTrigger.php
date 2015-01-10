<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class IssueOrderNumTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_ChangeRequest' ) return;
	    
	    if ( !in_array($kind, array(TRIGGER_ACTION_ADD, TRIGGER_ACTION_MODIFY)) ) return;

	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$to_process_ordernum =  
			$methodology_it->get('IsRequestOrderUsed') == 'Y' && array_key_exists('OrderNum', $content);
			
		if ( !$to_process_ordernum ) return;
		
		$this->processOrderNum($object_it);
	}
	
	function processOrderNum( $object_it )
	{
		$seq_it = getFactory()->getObject(get_class($object_it->object))->getRegistry()->Query(
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
		
		$sql = "UPDATE pm_ChangeRequest t SET t.OrderNum = @r:= (@r+1) WHERE t.pm_ChangeRequestId IN (".join(",",$ids).") ORDER BY t.OrderNum ASC";
		
		DAL::Instance()->Query( $sql );
		
		$sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
		       " SELECT NOW(), NOW(), t.VPD, t.pm_ChangeRequestId, 'Request' ".
		       "     FROM pm_ChangeRequest t WHERE t.pm_ChangeRequestId IN (".join(",",$ids).") ";
		 
		DAL::Instance()->Query( $sql );
	}
}
 