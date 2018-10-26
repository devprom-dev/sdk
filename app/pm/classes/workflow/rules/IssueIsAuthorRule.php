<?php
include_once "BusinessRulePredicate.php";

class IssueIsAuthorRule extends BusinessRulePredicate
{
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
 	function getDisplayName() {
 		return text(2135);
 	}
 	
 	function check( $object_it, $transitionIt ) {
 		return $object_it->get('Author') == getSession()->getUserIt()->getId();
 	}
}
