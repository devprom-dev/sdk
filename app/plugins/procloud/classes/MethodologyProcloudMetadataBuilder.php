<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class MethodologyProcloudMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Methodology' ) return;

    	$metadata->setAttributeVisible( 'IsHelps', false );
    	$metadata->setAttributeVisible( 'IsRequirements', false );
    	$metadata->setAttributeVisible( 'IsTests', false );
    }
}
