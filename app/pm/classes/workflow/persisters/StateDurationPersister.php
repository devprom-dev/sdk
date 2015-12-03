<?php

class StateDurationPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
		$terminal_states = ','.join(',',$this->getObject()->getTerminalStates()).',';

 		array_push( $columns, 
 			"( SELECT UNIX_TIMESTAMP(NOW()) / 3600 - ".
 			"    IFNULL( (SELECT UNIX_TIMESTAMP(MAX(so.RecordCreated)) ".
 			"               FROM pm_StateObject so WHERE so.pm_StateObjectId = ".$alias.".StateObject), ".
 			"            UNIX_TIMESTAMP(".$alias.".RecordCreated)) / 3600 ) StateDuration " );

		array_push( $columns,
			"(SELECT IFNULL( ".
			" 			SUM(so.Duration) + IF(INSTR('".$terminal_states."', ".$alias.".State) < 1, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(MAX(so.RecordCreated))) / 3600,0), ".
			"  			IF(INSTR('".$terminal_states."', ".$alias.".State) < 1, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(".$alias.".RecordCreated)) / 3600, 0) ) ".
			"   FROM pm_StateObject so, pm_State st, pm_Transition tr ".
			"  WHERE so.ObjectId = ".$this->getPK($alias).
			"    AND so.ObjectClass = '".$this->getObject()->getStatableClassName()."' ".
			"    AND tr.pm_TransitionId = so.Transition ".
			"    AND st.pm_StateId = tr.SourceState ".
			"    AND st.IsTerminal = 'N' ) LeadTime " );

 		return $columns;
 	}
 	
 	function modify( $object_id, $parms )
 	{
 		if ( !$parms['PersistStateDuration'] ) return;

 		$object = $this->getObject();
		
		$objectstate = getFactory()->getObject('pm_StateObject');
		$objectstate->addSort( new SortRecentClause() );
		
		$it = $objectstate->getByRefArray(
			array ( 'ObjectId' => $object_id,
					'ObjectClass' => $object->getStatableClassName() ), 2);
		
		if ( $it->count() > 1 ) $it->moveNext();
		$objectstate->modify_parms($it->getId(), array( 'Duration' => $parms['StateDuration'] ));
 	}
}