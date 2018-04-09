<?php
use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class BusinessActionIssueAutoActionWorkflow extends BusinessActionWorkflow
{
	private $action_it = null;
	
	function __construct( $action_it ) {
		$this->action_it = $action_it;
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
 		if ( !$this->checkConditions($this->action_it, $object_it) ) return false;
 		return $this->process($this->action_it, $object_it);
 	}
}
