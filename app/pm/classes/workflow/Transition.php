<?php

include "TransitionIterator.php";
include "predicates/TransitionObjectPredicate.php";
include "predicates/TransitionStateClassPredicate.php";
include "predicates/TransitionWasPredicate.php";
include "sorts/TransitionSourceStateSort.php";
include "predicates/TransitionStateRelatedPredicate.php";

class Transition extends Metaobject
{
 	function __construct() {
 		parent::__construct('pm_Transition');
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
