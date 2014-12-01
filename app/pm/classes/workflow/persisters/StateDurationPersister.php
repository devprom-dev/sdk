<?php

class StateDurationPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"( SELECT UNIX_TIMESTAMP(NOW()) / 3600 - ".
 			"    IFNULL( (SELECT UNIX_TIMESTAMP(MAX(so.RecordCreated)) ".
 			"               FROM pm_StateObject so WHERE so.pm_StateObjectId = ".$alias."StateObject), ".
 			"            UNIX_TIMESTAMP(".$alias."RecordCreated)) / 3600 ) StateDuration " );

 		array_push( $columns, 
 			"( SELECT s.Caption FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias."State AND s.VPD = ".$alias."VPD AND s.ObjectClass = '".get_class($this->getObject())."') StateName " );

 		array_push( $columns, 
 			"( SELECT s.RelatedColor FROM pm_State s ".
 			"   WHERE s.ReferenceName = ".$alias."State AND s.VPD = ".$alias."VPD AND s.ObjectClass = '".get_class($this->getObject())."') StateColor " );
 		
 		return $columns;
 	}
 	
 	function modify( $object_id, $parms )
 	{
 		global $model_factory;
		
 		if ( !$parms['PersistStateDuration'] ) return;

 		$object = $this->getObject();
		
		$objectstate = $model_factory->getObject('pm_StateObject');
		$objectstate->addSort( new SortRecentClause() );
		
		$it = $objectstate->getByRefArray(
			array ( 'ObjectId' => $object_id,
					'ObjectClass' => $object->getStatableClassName() ), 2);
		
		if ( $it->count() > 1 )
		{
			$it->moveNext();
			$objectstate->modify_parms($it->getId(), array( 'Duration' => $parms['StateDuration'] )); 
		}
 	}
}