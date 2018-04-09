<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class BusinessActionTaskIssueAutoActionWorkflow extends BusinessActionWorkflow
{
	private $action_it = null;
	
	function __construct( $action_it ) {
		$this->action_it = $action_it;
	}
	
 	function getId() {
 		return $this->action_it->get('ReferenceName').'4task';
 	}

 	function getDisplayName() {
 		return text(2504).': '.$this->action_it->getDisplayName();
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Task');
 	}
 	
	function apply( $object_it )
 	{
 	    if ( $object_it->get('ChangeRequest') < 1 ) return;
 	    if ( $object_it->object->getAttributeType('ChangeRequest') == '' ) return;

 	    $requestIt = $object_it->getRef('ChangeRequest');
 		if ( !$this->checkConditions($this->action_it, $requestIt) ) return false;

 		return $this->process($this->action_it, $requestIt);
 	}
}
