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

 		$state_it = $this->getObject()->cacheStates();
		
 		array_push( $columns, 
 			"IFNULL(( SELECT s.Caption FROM pm_State s, pm_StateObject so ".
 			"   WHERE s.pm_StateId = so.State ".
 			"     AND so.pm_StateObjectId = ".$alias."StateObject), '".$state_it->getDisplayName()."') StateName " );
		
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