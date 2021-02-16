<?php

class AffirmationStateFilter extends FilterPredicate
{
 	function _predicate( $filter )
    {
        $transitionIt = getFactory()->getObject('Transition')->getRegistry()->Query(
            array(
                new FilterBaseVpdPredicate(),
                new TransitionStateClassPredicate($this->getObject()->getStatableClassName()),
                new TransitionCyclicStatePredicate()
            )
        );
        if ( $transitionIt->count() < 1 ) return " AND 1 = 2 ";

        $stateIt = $transitionIt->getRef('SourceState');
        switch( $filter ) {
            case 'ready':
                return " AND t.State = '".$stateIt->get('ReferenceName')."' 
                         AND EXISTS (SELECT 1 FROM pm_StateObject so, pm_StateObject so2 
                                      WHERE so.pm_StateObjectId = t.StateObject
                                        AND so.Transition = ".$transitionIt->getId()."
                                        AND so.Transition = so2.Transition
                                        AND so.ObjectId = so2.ObjectId
                                        AND so2.Author = ".getSession()->getUserIt()->getId().") ";
            case 'myturn':
                return " AND t.State = '".$stateIt->get('ReferenceName')."' 
                         AND NOT EXISTS 
                                    (SELECT 1 FROM pm_StateObject so, pm_StateObject so2 
                                      WHERE so.pm_StateObjectId = t.StateObject
                                        AND so.Transition = ".$transitionIt->getId()."
                                        AND so.Transition = so2.Transition
                                        AND so.ObjectId = so2.ObjectId
                                        AND so2.Author = ".getSession()->getUserIt()->getId().") ";
            default:
                return " AND 1 = 1 ";
        }
 	}
}
