<?php

class StatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();

		switch ( $filter )
		{
			case 'notresolved':
			case 'notterminal':
				return " AND IFNULL(".$this->getAlias().".State,'submitted') NOT IN ('".
					join($object->getTerminalStates(), "','")."') ";

			case 'terminal':
				return " AND IFNULL(".$this->getAlias().".State,'submitted') IN ('".
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