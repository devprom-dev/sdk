<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ProjectMetadataCodeBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Project' ) return;

    	$metadata->addAttribute( 'IsSubversionUsed', 'CHAR', "text(sourcecontrol8)", true, true, "text(sourcecontrol7)" );
    }
}