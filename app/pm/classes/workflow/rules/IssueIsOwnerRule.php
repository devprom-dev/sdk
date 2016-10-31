<?php

include_once "BusinessRulePredicate.php";

class IssueIsOwnerRule extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(1145);
 	}
 	
 	function check( $object_it ) {
 		return $object_it->get('Owner') == getSession()->getUserIt()->getId();
 	}
}
