<?php

class TransitionWasPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$transition_it = getFactory()->getObject('Transition')->getExact(preg_split('/,/',$filter));
 		
 		if ( $transition_it->getId() == '' ) return " AND 1 = 2 ";
 		
 		return " AND EXISTS (SELECT 1 FROM pm_StateObject s " .
 			   "			  WHERE s.ObjectId = t." .$this->getObject()->getIdAttribute().
 			   "				AND s.Transition IN (".join(',',$transition_it->idsToArray()).")) ";
 	}
}
