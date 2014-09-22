<?php

class StateNeighborsPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		$state = $model_factory->getObject('pm_State');
 		$state_it = $state->getExact( $filter );
 		
 		if ( $state_it->count() > 0 )
 		{
			return " AND t.ObjectClass = '".strtolower($state_it->get('ObjectClass'))."'" .
				   " AND t.pm_StateId <> ".$state_it->getId();
 		}
 		else
 		{
 			return " AND 1 = 2 ";
 		}
 	}
} 
