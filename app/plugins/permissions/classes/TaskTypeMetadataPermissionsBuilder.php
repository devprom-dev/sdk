<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class TaskTypeMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof TaskType ) return;

    	$metadata->setAttributeVisible('ProjectRole', true);
		$metadata->setAttributeDescription('ProjectRole', text(1849));
    }
}