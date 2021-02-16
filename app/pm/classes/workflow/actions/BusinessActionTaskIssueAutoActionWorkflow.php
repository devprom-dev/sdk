<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class BusinessActionTaskIssueAutoActionWorkflow extends BusinessActionWorkflow
{
	private $action_it = null;
    private $action_id = 0;

	function __construct( $action_it ) {
		$this->action_it = $action_it;
        $this->action_id = $this->action_it->getId();
	}

    public function __sleep() {
        return array('action_id');
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

        if ( $this->action_id < 1 ) return false;
        $actionIt = getFactory()->getObject('IssueAutoAction')->getExact($this->action_id);

 	    $requestIt = $object_it->getRef('ChangeRequest');
 		if ( !$this->checkConditions($actionIt, $requestIt) ) return false;

 		return $this->process($actionIt, $requestIt);
 	}
}
