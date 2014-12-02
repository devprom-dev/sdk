<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class StateKanbanMetadataBuilder extends ObjectMetadataEntityBuilder 
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
		
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof IssueState ) return;

    	if ( $this->session->getProjectIt()->getMethodologyIt()->get('IsKanbanUsed') != 'Y' ) return;
    	    	
 		$metadata->addAttribute( 'QueueLength', 'INTEGER', text('kanban2'), true, true, text('kanban3') );
    }
}
