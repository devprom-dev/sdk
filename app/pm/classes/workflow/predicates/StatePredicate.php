<?php

class StatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$object = $this->getObject();

		switch ( $filter )
		{
			case 'notresolved':
			case 'notterminal':
				return " AND IFNULL(t.State,'submitted') NOT IN ('".
					join($object->getTerminalStates(), "','")."') ";

			case 'terminal':
				return " AND IFNULL(t.State,'submitted') IN ('".
					join($object->getTerminalStates(), "','")."') ";
				
			default:
		 		$state = $model_factory->getObject($object->getStateClassName());
		 		
		 		$state_it = $state->getByRefArray( array (
		 			'ReferenceName' => preg_split('/[,-]/', $filter) 
		 		));
		 		
		 		if ( $state_it->count() > 0 )
		 		{
		 			return " AND t.State IN ('".
		 				join($state_it->fieldToArray('ReferenceName'), "','")."')";
		 		}
		 		else
		 		{
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