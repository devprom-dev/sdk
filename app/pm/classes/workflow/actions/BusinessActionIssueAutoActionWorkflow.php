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
 		if ( !$this->checkConditions($object_it) ) return false;

        $parms = array();
 		foreach($this->action_it->object->getActionAttributes() as $attribute)
 		{
 			if ( $this->action_it->get($attribute) == '' ) continue;
 			$parms[$attribute] = $this->action_it->getHtmlDecoded($attribute);
 			if ( $attribute == 'State' ) {
                $parms['TransitionComment'] = $this->action_it->getDisplayName();
            }
 		}

 		if ( count($parms) < 1 ) return true;

 		$object_it->object->setNotificationEnabled(true);
 		$object_it->object->modify_parms( $object_it->getId(), $parms );
 		
 		return true;
 	}

 	protected function checkConditions( $object_it )
 	{
 		return ModelService::queryXPath(
 					$object_it->copyAll(),
 					$this->action_it->getConditionXPath()
 			)->count() > 0;
 	}
}
