<?php
include_once "BusinessRulePredicate.php";

class IssueBlockedByOpenTasks extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(2118);
 	}
 	
 	function check( $object_it ) {
		return $object_it->get('OpenTasks') == '';
 	}
 	
 	function getNegativeReason() {
 		return text(2119);
 	}
}
