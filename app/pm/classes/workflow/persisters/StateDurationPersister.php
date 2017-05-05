<?php

class StateDurationPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('StateDuration', 'LeadTime');
	}

	function getSelectColumns( $alias )
 	{
 		$columns = array();
		$terminal_states = ','.join(',',$this->getObject()->getTerminalStates()).',';

 		array_push( $columns,
 			"( SELECT UNIX_TIMESTAMP(NOW()) / 3600 - ".
 			"    IFNULL( (SELECT UNIX_TIMESTAMP(MAX(self.RecordCreated)) / 3600 - IFNULL(SUM(so.Duration),0) ".
 			"               FROM pm_StateObject so, pm_StateObject self ".
			"			   WHERE self.pm_StateObjectId = ".$alias.".StateObject ".
			"     		     AND self.State = so.State ".
			"     			 AND self.ObjectId = so.ObjectId), ".
 			"            UNIX_TIMESTAMP(".$alias.".RecordCreated)/ 3600 ) ) StateDuration " );

		array_push( $columns,
			"( SELECT UNIX_TIMESTAMP(NOW()) / 3600 - ".
			"    IFNULL( (SELECT UNIX_TIMESTAMP(MAX(self.RecordCreated)) / 3600 ".
			"               FROM pm_StateObject self ".
			"			   WHERE self.pm_StateObjectId = ".$alias.".StateObject), ".
			"            UNIX_TIMESTAMP(".$alias.".RecordCreated)/ 3600 ) ) StateDurationRecent " );

        if ( $this->getObject()->getAttributeType('LifecycleDuration') != '' ) {
            array_push( $columns,
                "(IFNULL(".$alias.".LifecycleDuration, 0) + 
			    IF(INSTR('".$terminal_states."', ".$alias.".State) < 1, 
			        (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP((SELECT IFNULL(MAX(so.RecordCreated), ".$alias.".RecordCreated) FROM pm_StateObject so WHERE so.pm_StateObjectId = ".$alias.".StateObject) )) / 3600, 0
			    )
			  ) LeadTime " );
        }
        else {
            array_push( $columns,
                "(SELECT IFNULL( ".
                " 			SUM(so.Duration) + IF(INSTR('".$terminal_states."', ".$alias.".State) < 1, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(MAX(so.RecordCreated))) / 3600,0), ".
                "  			IF(INSTR('".$terminal_states."', ".$alias.".State) < 1, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(".$alias.".RecordCreated)) / 3600, 0) ) ".
                "   FROM pm_StateObject so, pm_State st, pm_Transition tr ".
                "  WHERE so.ObjectId = ".$this->getPK($alias).
                "    AND so.ObjectClass = '".$this->getObject()->getStatableClassName()."' ".
                "    AND tr.pm_TransitionId = so.Transition ".
                "    AND st.pm_StateId = tr.SourceState ".
                "    AND st.IsTerminal <> 'Y' ) LeadTime " );
        }

 		return $columns;
 	}
}