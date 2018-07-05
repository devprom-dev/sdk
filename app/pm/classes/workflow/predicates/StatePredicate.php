<?php

class StatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
        $metaStates = array('N','I','Y');
        $states = array_intersect(preg_split('/[-,]/', $filter), $metaStates);
        if ( count($states) > 0 ) {
            return " AND (".$this->getAlias().".State, ".$this->getAlias().".VPD) IN (
                       SELECT s.ReferenceName, s.VPD FROM pm_State s 
                        WHERE s.IsTerminal IN ('".join($states,"','")."') 
                          AND s.ObjectClass = '".get_class($this->getObject())."' ) ";
        }

 		$object = $this->getObject();
 		$states = \WorkflowScheme::Instance()->getNonTerminalStates($object);

		switch ( $filter )
		{
			case 'notresolved':
			case 'notterminal':
				return " AND IFNULL(".$this->getAlias().".State,'".$states[0]."') NOT IN ('".
					join($object->getTerminalStates(), "','")."') ";

			case 'terminal':
				return " AND IFNULL(".$this->getAlias().".State,'".$states[0]."') IN ('".
					join($object->getTerminalStates(), "','")."') ";
				
			default:
				$states = array_filter(preg_split('/[,-]/', $filter), function($state) {
					return preg_match('/[A-Za-z0-9_]/', $state);
				});
		 		if ( count($states) > 0 ) {
		 			return " AND ".$this->getAlias().".State IN ('".join($states,"','")."')";
		 		}
		 		else {
		 			return " AND 1 = 2 ";
		 		}
		}
 	}
 	
 	function get( $filter )
 	{
 		$instance = new StatePredicate( $filter );
 		return $instance->getPredicate();
 	}
} 