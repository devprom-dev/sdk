<?php

include "TransitionPredicateIterator.php";

class TransitionPredicate extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_TransitionPredicate');
 		$this->setAttributeType('Predicate', 'REF_StateBusinessRuleId');
 	}
 	
 	function createIterator() 
 	{
 		return new TransitionPredicateIterator( $this );
 	}
}
