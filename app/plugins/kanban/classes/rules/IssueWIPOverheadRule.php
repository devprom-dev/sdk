<?php
include_once SERVER_ROOT_PATH . "pm/classes/workflow/rules/BusinessRulePredicate.php";

class IssueWIPOverheadRule extends BusinessRulePredicate
{
    private $stateIt = null;

 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
 	function getDisplayName() {
 		return text('kanban34');
 	}
 	
 	function check( $object_it, $transitionIt ) {
 	    $this->stateIt = $transitionIt->getRef('TargetState');
 		return $this->stateIt->getObjectsCount() < $this->stateIt->get('QueueLength');
 	}

    function getNegativeReason() {
        return preg_replace('/%1/', $this->stateIt->getDisplayName(), text('kanban35'));
    }
}
