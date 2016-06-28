<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/KanbanBlockReasonPersister.php";

class RequestKanbanMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Request) return;
		$metadata->addPersister( new KanbanBlockReasonPersister() );
    }
}
