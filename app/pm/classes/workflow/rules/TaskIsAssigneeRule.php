<?php

include_once "BusinessRulePredicate.php";

class TaskIsAssigneeRule extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_Task');
 	}
 	
 	function getDisplayName() {
 		return text(1144);
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('Assignee') == getSession()->getUserIt()->getId();
 	}
}
