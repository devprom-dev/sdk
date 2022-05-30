<?php

class TransitionObjectPredicate extends FilterPredicate
{
 	private $object;

 	function TransitionObjectPredicate( $object, $filter ) {
 		$this->object = $object;
 		parent::__construct( $filter );
 	}
 	
 	function _predicate( $filter )
 	{
 		$ids = \TextUtils::parseFilterItems($filter);
 		if ( count($ids) < 1 ) return " AND 1 = 1 ";
 		
        $transition_it = getFactory()->getObject('Transition')->getExact($ids);
        if ( $transition_it->count() < 1 ) return " AND 1 = 2 ";

        return " AND EXISTS (SELECT 1 FROM pm_StateObject so " .
               "  			  WHERE so.pm_StateObjectId = t.StateObject ".
               "			    AND so.Transition IN (".join(',',$transition_it->idsToArray()).") )";
 	}
} 