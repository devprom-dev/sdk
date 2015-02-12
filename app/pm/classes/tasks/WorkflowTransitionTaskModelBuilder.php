<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WorkflowTransitionTaskModelBuilder extends ObjectModelBuilder 
{
	private $transition_it = null;
	
	public function __construct( $transition_it )
	{
		$this->transition_it = $transition_it;
	}
	
    public function build( Metaobject $object )
    {
    	$target_it = $this->transition_it->getRef('TargetState');
    	
		if ( $target_it->get('IsTerminal') == 'Y' )
		{
			if ( !getSession()->getProjectIt()->getMethodologyIt()->IsParticipantsTakesTasks() )
			{
				$object->setAttributeRequired('Assignee', true);	
				$object->setAttributeVisible('Assignee', true);
			}
		}
    }
}