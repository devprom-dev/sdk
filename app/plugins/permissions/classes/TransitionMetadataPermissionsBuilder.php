<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class TransitionMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Transition ) return;
    	$metadata->setAttributeVisible('ProjectRoles', true);
    }
}