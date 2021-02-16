<?php

class IssueOrderNumTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_ChangeRequest' ) return;
	    if ( $kind == TRIGGER_ACTION_MODIFY && array_key_exists('OrderNum', $content) ) {
            $this->processOrderNum($object_it);
        }
	}
	
	function processOrderNum( $object_it )
	{
        $minimalIndex = abs($object_it->get('OrderNum') > 0 ? $object_it->get('OrderNum') : 0);
		$sql = "SET @r=" . $minimalIndex;
		DAL::Instance()->Query( $sql );
		
		$sql = "UPDATE pm_ChangeRequest t SET t.OrderNum = @r:= (@r+1) WHERE t.FinishDate IS NULL AND t.OrderNum >= ".$minimalIndex." ORDER BY t.OrderNum ASC";
		DAL::Instance()->Query( $sql );

		$sql = "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, VPD, ObjectId, ObjectClass) ".
		       " SELECT NOW(), NOW(), t.VPD, t.pm_ChangeRequestId, 'Request' ".
		       "     FROM pm_ChangeRequest t WHERE t.FinishDate IS NULL AND t.OrderNum >= ".$minimalIndex;
		DAL::Instance()->Query( $sql );
	}
}
 