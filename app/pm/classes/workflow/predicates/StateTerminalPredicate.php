<?php

class StateTerminalPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$object = $this->getObject();
 		
 		switch ( $filter )
 		{
 			case 'terminal':
				return " AND EXISTS ( SELECT 1 FROM pm_State s" .
					   "				   WHERE s.ObjectClass = '".$object->getStatableClassName()."' " .
					   "				 	 AND s.ReferenceName = t.State " .
					   "				     AND s.IsTerminal = 'Y' )";

 			case 'notterminal':
				return " AND NOT EXISTS ( SELECT 1 FROM pm_State s" .
					   "				   WHERE s.ObjectClass = '".$object->getStatableClassName()."' " .
					   "				 	 AND s.ReferenceName = t.State " .
					   "				     AND s.IsTerminal = 'Y' )";
 		}
 	}
} 
