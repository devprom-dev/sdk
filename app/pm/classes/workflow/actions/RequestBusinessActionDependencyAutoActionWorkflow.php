<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class RequestBusinessActionDependencyAutoActionWorkflow extends BusinessActionWorkflow
{
	private $action_it = null;
	
	function __construct( $action_it ) {
		$this->action_it = $action_it;
	}
	
 	function getId() {
 		return $this->action_it->get('ReferenceName').'4dependsissue';
 	}

 	function getDisplayName() {
 		return text(2522).': '.$this->action_it->getDisplayName();
 	}
 	
 	function getObject() {
 		return getFactory()->getObject('pm_ChangeRequest');
 	}
 	
	function apply( $object_it )
 	{
        $duplicate_it = $this->getObject()->getRegistry()->Query(
            array (
                new RequestDependsFilter($object_it->getId())
            )
        );
        while( !$duplicate_it->end() ) {
            $requestIt = $duplicate_it->copy();
            if ( !$this->checkConditions($this->action_it, $requestIt) ) return false;
            $this->process($this->action_it, $requestIt);
            $duplicate_it->moveNext();
        }

        return true;
 	}
}
