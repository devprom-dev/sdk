<?php
include_once "RequestBusinessActionChangeStateBase.php";

class RequestBusinessActionMoveImplementedNextState extends RequestBusinessActionChangeStateBase
{
 	function getId() {
 		return '6c22bd92-8af5-4644-91b6-84f2219b57e7';
 	}

    function getFilters($object_it) {
        return array (
            new RequestImplementationFilter($object_it->getId())
        );
    }

    function getStateFilters($object_it)
    {
        $state_it = $object_it->getStateIt();
        if ( $state_it->get('IsTerminal') == 'Y' ) {
            return array(
                new FilterInPredicate(-1)
            );
        }

        $transition_it = getFactory()->getObject('Transition')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('SourceState', $state_it->getId()),
                new SortOrderedClause()
            )
        );
        while( !$transition_it->end() ) {
            if ( !$transition_it->doable($object_it) ) {
                $transition_it->moveNext();
                continue;
            }
            return array(
                new FilterInPredicate($transition_it->get('TargetState'))
            );
        }

        return array(
            new FilterInPredicate(-1)
        );
    }

 	function getDisplayName() {
 		return text(2102);
 	}
}
