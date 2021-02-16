<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH."pm/classes/workflow/actions/BusinessActionWorkflow.php";

class RequestBusinessActionDependencyAutoActionWorkflow extends BusinessActionWorkflow
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
        if ( $this->action_id < 1 ) return false;
        $actionIt = getFactory()->getObject('IssueAutoAction')->getExact($this->action_id);

        $duplicate_it = $this->getObject()->getRegistry()->Query(
            array (
                new RequestDependsFilter($object_it->getId())
            )
        );
        while( !$duplicate_it->end() ) {
            $requestIt = $duplicate_it->copy();
            if ( !$this->checkConditions($actionIt, $requestIt) ) return false;
            $this->process($actionIt, $requestIt);
            $duplicate_it->moveNext();
        }

        return true;
 	}
}
