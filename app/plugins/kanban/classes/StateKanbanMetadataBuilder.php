<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class StateKanbanMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof IssueState && !$metadata->getObject() instanceof TaskState ) return;
 		$metadata->addAttribute( 'QueueLength', 'INTEGER', text('kanban2'), true, true, text('kanban3') );
    }
}
