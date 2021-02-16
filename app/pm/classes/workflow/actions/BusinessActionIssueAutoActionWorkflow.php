<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class BusinessActionIssueAutoActionWorkflow extends BusinessActionWorkflow
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
 		return $this->action_it->get('ReferenceName');
 	}

 	function getDisplayName() {
 		return text(2434).': '.$this->action_it->getDisplayName();
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('Request');
 	}
 	
	function apply( $object_it )
 	{
 	    if ( $this->action_id < 1 ) return false;
 	    $actionIt = getFactory()->getObject('IssueAutoAction')->getExact($this->action_id);
 		if ( !$this->checkConditions($actionIt, $object_it) ) return false;
 		return $this->process($actionIt, $object_it);
 	}
}
