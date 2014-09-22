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
	    global $model_factory;
	    
		$object = $model_factory->getObject(get_class($object_it->object));
 			
		$object->addSort( new SortOrderedClause() );
		
		$object->addFilter( new StatePredicate('notresolved') );
		
		$object->addFilter( new FilterNextSiblingsPredicate($object_it) );
		
		$seq_it = $object->getAll();
		
		if ( $seq_it->count() < 1 ) return;
		
		$sql = "SET @r=".($object_it->get('OrderNum') > 0 ? $object_it->get('OrderNum') : 0);
		 
		DAL::Instance()->Query( $sql );
		
		$sql = "UPDATE pm_ChangeRequest t SET t.OrderNum = @r:= (@r+1), t.RecordModified = NOW() WHERE t.pm_ChangeRequestId IN (".join(",", $seq_it->idsToArray()).") ORDER BY t.OrderNum ASC";
		
		DAL::Instance()->Query( $sql );
		
		$sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
		       " SELECT NOW(), NOW(), t.VPD, t.pm_ChangeRequestId, 'Request' ".
		       "     FROM pm_ChangeRequest t WHERE t.pm_ChangeRequestId IN (".join(",", $seq_it->idsToArray()).") ";
		 
		DAL::Instance()->Query( $sql );
	}
}
 