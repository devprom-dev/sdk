<?php

class TransitionObjectPredicate extends FilterPredicate
{
 	var $object;
 	
 	function TransitionObjectPredicate( $object, $filter )
 	{
 		$this->object = $object;
 		
 		parent::FilterPredicate( $filter );
 	}
 	
 	function _predicate( $filter )
 	{
 		global $model_factory;
 		
 		switch ( $filter )
 		{
 			case 'all':
 				return "";
 				
 			default:
		 		$transition = $model_factory->getObject('Transition');
		 		$transition_it = $transition->getExact( preg_split('/,/', $filter) );
		 		
		 		if ( $transition_it->count() > 0 )
		 		{
		 			return " AND EXISTS (SELECT 1 FROM pm_StateObject so " .
		 				   "  			  WHERE so.pm_StateObjectId = t.StateObject ".
		 				   "			    AND so.Transition IN (".join(',',$transition_it->idsToArray()).") )";
		 		}
		 		else
		 		{
		 			return " AND 1 = 2 ";
		 		}
 		}
 	}
} 