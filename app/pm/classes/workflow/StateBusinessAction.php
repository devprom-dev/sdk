<?php
include "StateBusinessActionIterator.php";
include "StateBusinessActionRegistry.php";
include_once "predicates/StateBusinessEntityFilter.php";

class StateBusinessAction extends MetaobjectCacheable
{
 	function __construct() {
 		parent::__construct('pm_Predicate',
            new StateBusinessActionRegistry($this));
 	}
 	
 	function createIterator() {
 		return new StateBusinessActionIterator( $this );
 	}

    function IsPersistable() {
        return false;
    }
}