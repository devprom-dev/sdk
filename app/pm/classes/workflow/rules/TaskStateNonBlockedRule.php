<?php

include_once "BusinessRulePredicate.php";

class TaskStateNonBlockedRule extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(1143);
 	}
 	
 	function check( $object_it ) {
 		return !$object_it->IsBlocked();
 	}
 	
 	function getNegativeReason() {
 		return text(875);
 	}
}
