<?php
include "StateBusinessRuleIterator.php";
include "StateBusinessRuleRegistry.php";
include_once "predicates/StateBusinessEntityFilter.php";
include "predicates/StateRuleStateClassPredicate.php";

class StateBusinessRule extends MetaobjectCacheable
{
 	function __construct( $entity = null ) {
 		parent::__construct('pm_Predicate', new StateBusinessRuleRegistry($this));
 	}
 	
 	function createIterator() {
 		return new StateBusinessRuleIterator( $this );
 	}

    function IsPersistable() {
 	    return false;
    }
}