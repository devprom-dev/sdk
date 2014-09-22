<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ProjectMetadataFilesBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Project' ) return;

    	$metadata->addAttribute( 'IsFileServer', 'CHAR', "text(operations2)", true, true, "text(operations3)" );
    }
}