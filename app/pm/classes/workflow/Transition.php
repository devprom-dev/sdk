<?php
include "TransitionIterator.php";
include "predicates/TransitionObjectPredicate.php";
include "predicates/TransitionStateClassPredicate.php";
include "predicates/TransitionSourceStatePredicate.php";
include "predicates/TransitionWasPredicate.php";
include "sorts/TransitionTargetStateSort.php";
include "predicates/TransitionStateRelatedPredicate.php";
include "predicates/TransitionCyclicStatePredicate.php";
include "predicates/TransitionAfterStatePredicate.php";

class Transition extends Metaobject
{
 	function __construct() {
 		parent::__construct('pm_Transition');
 		$this->setSortDefault(
 		    array(
 		        new SortOrderedClause(),
 		        new TransitionTargetStateSort()
            )
        );
 	}
 	
 	function createIterator() {
 		return new TransitionIterator( $this );
 	}
 	
 	function setStateAttributeType( $state )
 	{
 		$this->setAttributeType( 'TargetState', 'REF_'.get_class($state).'Id' );
 		$this->setAttributeType( 'SourceState', 'REF_'.get_class($state).'Id' );
 	}
}
