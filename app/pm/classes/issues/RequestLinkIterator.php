<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateDurationPersister.php";

class RequestLinkIterator extends OrderedIterator
{
 	function getDisplayName() 
 	{
		$uid = new ObjectUID;
		
		$link_type_it = $this->getRef('LinkType');
		
		$target_it = $this->object->getAttributeObject('TargetRequest')->getRegistry()->Query(
			array (
					new FilterInPredicate($this->get('TargetRequest')),
					new StateDurationPersister()
			)
		);

		return $link_type_it->getDisplayName().': '.
			$uid->getUidIconGlobal($target_it).' '.$target_it->getDisplayName().' ('.$target_it->get('StateName').')';
 	}
 	
 	function getTraceDisplayName() 
 	{
		$uid = new ObjectUID;

		$link_type_it = $this->getRef('LinkType');
		
		$target_it = $this->getRef('TargetRequest');

		return $link_type_it->getDisplayName().': '.$uid->getObjectUid($target_it).' '.$target_it->getDisplayName();
 	}

 	function getRequestIt( $field_name )
 	{
 		global $model_factory;
 		
 		$sql = " SELECT r.* " .
 			   "   FROM pm_ChangeRequest r, pm_ChangeRequestLink t " .
 			   "  WHERE r.pm_ChangeRequestId = t.".$field_name.
 			   "    AND t.pm_ChangeRequestLinkId = ".$this->getId();
 			   
 		$request = $model_factory->getObject('pm_ChangeRequest');
 		return $request->createSQLIterator( $sql );
 	}
}