<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/CapacityPersister.php";

class IterationMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Release' ) return;

    	$metadata->addPersister( new CapacityPersister() );
    	
    	$metadata->setAttributeRequired('FinishDate', !getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease()); 
    	$metadata->setAttributeRequired('InitialVelocity', false);
    }
}