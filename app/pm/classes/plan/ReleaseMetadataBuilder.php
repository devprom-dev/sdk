<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/CapacityPersister.php";

class ReleaseMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( ! $metadata->getObject() instanceof Release ) return;

    	$metadata->addAttribute( 'PlannedCapacity', 'FLOAT', text(1421), false );
        
        $metadata->addAttribute( 'LeftCapacityInWorkingDays', 'FLOAT', text(1422), false );
        
 	    $metadata->addPersister( new CapacityPersister() );
    }
}